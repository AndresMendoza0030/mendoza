<?php

namespace App\Services;

use App\ClienteProveedor;
use App\Inventoryhead;
use App\Movdetail;
use App\Product;
use App\ProductAlias;
use App\PurchaseImport;
use App\PurchaseImportItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseImportService
{
    protected ?Collection $productCatalog = null;

    public function createDraftFromOcr(array $ocrResult, ?string $imagePath, ?int $userId): PurchaseImport
    {
        $header = $ocrResult['header'] ?? [];
        $totals = $ocrResult['totals'] ?? [];
        $items = $ocrResult['items'] ?? [];

        return DB::transaction(function () use ($header, $totals, $items, $imagePath, $userId, $ocrResult) {
            $purchaseImport = PurchaseImport::create([
                'supplier_name' => $header['supplier_name'] ?? null,
                'supplier_nit' => $header['supplier_nit'] ?? null,
                'supplier_nrc' => $header['supplier_nrc'] ?? null,
                'document_number' => $header['document_number'] ?? null,
                'control_number' => $header['control_number'] ?? null,
                'generation_code' => $header['generation_code'] ?? null,
                'issue_date' => $header['issue_date'] ?? null,
                'subtotal' => $totals['subtotal'] ?? null,
                'iva' => $totals['iva'] ?? null,
                'total' => $totals['total'] ?? null,
                'payment_terms' => $header['payment_terms'] ?? null,
                'raw_text' => $ocrResult['raw_text'] ?? null,
                'image_path' => $imagePath,
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            foreach (array_values($items) as $index => $itemData) {
                [$matchedProductId, $matchScore] = $this->findBestProductMatch(
                    $purchaseImport->supplier_name,
                    $itemData['description'] ?? null
                );

                PurchaseImportItem::create([
                    'purchase_import_id' => $purchaseImport->id,
                    'line_no' => $itemData['line_no'] ?? ($index + 1),
                    'extracted_code' => $itemData['code'] ?? null,
                    'extracted_description' => $itemData['description'] ?? null,
                    'extracted_unit' => $itemData['unit'] ?? null,
                    'extracted_qty' => $itemData['qty'] ?? 0,
                    'extracted_unit_price' => $itemData['unit_price'] ?? 0,
                    'extracted_subtotal' => $itemData['subtotal'] ?? null,
                    'matched_product_id' => $matchedProductId,
                    'match_score' => $matchScore,
                    'needs_review' => !$matchedProductId,
                ]);
            }

            return $purchaseImport->load('items.product');
        });
    }

    public function updateReviewData(PurchaseImport $purchaseImport, array $items): void
    {
        foreach ($items as $itemData) {
            $item = PurchaseImportItem::where('purchase_import_id', $purchaseImport->id)
                ->findOrFail($itemData['id']);

            $qty = (float) $itemData['qty'];
            $unitPrice = (float) $itemData['unit_price'];

            $item->update([
                'matched_product_id' => $itemData['matched_product_id'] ?: null,
                'extracted_qty' => $qty,
                'extracted_unit_price' => $unitPrice,
                'extracted_subtotal' => round($qty * $unitPrice, 4),
                'needs_review' => empty($itemData['matched_product_id']),
            ]);
        }

        $purchaseImport->load('items');

        $purchaseImport->update([
            'subtotal' => round(
                $purchaseImport->items->sum(function (PurchaseImportItem $item) {
                    return (float) $item->extracted_qty * (float) $item->extracted_unit_price;
                }),
                4
            ),
            'status' => 'reviewed',
        ]);
    }

    public function confirmAsPurchaseEntry(
        PurchaseImport $purchaseImport,
        int $supplierId,
        int $warehouseId,
        ?int $userId
    ): void {
        $purchaseImport->loadMissing('items');

        $items = $purchaseImport->items
            ->filter(function (PurchaseImportItem $item) {
                return $item->matched_product_id && (float) $item->extracted_qty > 0;
            })
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Debe seleccionar al menos un producto para registrar la compra.',
            ]);
        }

        DB::transaction(function () use ($purchaseImport, $supplierId, $warehouseId, $userId, $items) {
            $supplier = ClienteProveedor::findOrFail($supplierId);
            $movementDate = optional($purchaseImport->issue_date)->format('Y-m-d') ?: now()->format('Y-m-d');
            $subtotal = round(
                $items->sum(function (PurchaseImportItem $item) {
                    return (float) $item->extracted_qty * (float) $item->extracted_unit_price;
                }),
                4
            );
            $iva = round((float) ($purchaseImport->iva ?? 0), 4);
            $total = (float) ($purchaseImport->total ?: round($subtotal + $iva, 2));

            $head = Inventoryhead::create([
                'bodega' => $warehouseId,
                'bodegadestino' => 0,
                'docucontra' => 0,
                'fecha' => $movementDate,
                'tipodoc' => 'CPRA',
                'paciente_id' => 0,
                'sujeto_id' => $supplier->id,
                'sujeto' => $supplier->razon,
                'estatus' => 0,
                'concepto' => 'COMPRA OCR ' . ($purchaseImport->document_number ?: ('OCR-' . $purchaseImport->id)),
                'fecha_doc' => $movementDate,
                'tpdocufiscal' => null,
                'numdocufiscal' => $this->extractNumericDocumentNumber(
                    $purchaseImport->document_number,
                    $purchaseImport->id
                ),
                'subtotal' => $subtotal,
                'gravado' => $subtotal,
                'iva' => $iva,
                'retencion' => 0,
                'percepcion' => 0,
                'total' => $total,
                'fiscal' => $purchaseImport->document_number ? 'SI' : 'NO',
                'user_id' => $userId,
            ]);

            $remainingIva = $iva;
            $lineCount = $items->count();
            $taxRate = $subtotal > 0 ? round(($iva / $subtotal) * 100, 2) : 0;

            foreach ($items as $index => $item) {
                $product = Product::findOrFail($item->matched_product_id);
                $lineSubtotal = round((float) $item->extracted_qty * (float) $item->extracted_unit_price, 4);

                if ($index === $lineCount - 1) {
                    $lineIva = $remainingIva;
                } else {
                    $lineIva = $subtotal > 0 ? round(($lineSubtotal / $subtotal) * $iva, 4) : 0;
                    $remainingIva = round($remainingIva - $lineIva, 4);
                }

                Movdetail::create([
                    'bodega' => $warehouseId,
                    'tipodoc' => $head->tipodoc,
                    'numerodoc' => $head->id,
                    'fecha' => $movementDate,
                    'paciente_id' => 0,
                    'clase' => $product->clase,
                    'linea' => $product->linea,
                    'lineafac' => $product->lineafac,
                    'producto_id' => $product->id,
                    'producto' => $product->nombre,
                    'anterior' => 0,
                    'cantidad' => $item->extracted_qty,
                    'posterior' => 0,
                    'precosto' => $item->extracted_unit_price,
                    'preventa' => $product->precioventa ?? 0,
                    'subtotal' => $lineSubtotal,
                    'gravado' => $lineSubtotal,
                    'iva' => $lineIva,
                    'total' => round($lineSubtotal + $lineIva, 2),
                    'lotevence' => 0,
                    'tasaiva' => $taxRate,
                    'revisado' => 0,
                    'estatus' => 0,
                    'movimiento' => 'E',
                    'user_id' => $userId,
                ]);
            }

            $purchaseImport->update([
                'status' => 'confirmed',
                'confirmed_by' => $userId,
            ]);
        });
    }

    protected function findBestProductMatch(?string $supplierName, ?string $description): array
    {
        $normalizedDescription = $this->normalizeForMatch($description);

        if ($normalizedDescription === '') {
            return [null, 0];
        }

        $bestProductId = null;
        $bestScore = 0;

        if (Schema::hasTable('product_aliases')) {
            $normalizedSupplier = $this->normalizeForMatch($supplierName);
            $aliases = ProductAlias::query()->get(['product_id', 'supplier_name', 'normalized_alias']);

            foreach ($aliases as $alias) {
                $aliasSupplier = $this->normalizeForMatch($alias->supplier_name);
                if ($normalizedSupplier !== '' && $aliasSupplier !== '' && $aliasSupplier !== $normalizedSupplier) {
                    continue;
                }

                $score = $this->scoreMatch($normalizedDescription, $this->normalizeForMatch($alias->normalized_alias));
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestProductId = $alias->product_id;
                }
            }
        }

        foreach ($this->getProductCatalog() as $product) {
            $score = $this->scoreMatch($normalizedDescription, $this->normalizeForMatch($product->nombre));
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestProductId = $product->id;
            }
        }

        if ($bestScore < 45) {
            return [null, $bestScore];
        }

        return [$bestProductId, $bestScore];
    }

    protected function getProductCatalog(): Collection
    {
        if ($this->productCatalog === null) {
            $this->productCatalog = Product::query()
                ->where('tipo', 'PRODUCTO')
                ->where('estatus', '0')
                ->get(['id', 'nombre']);
        }

        return $this->productCatalog;
    }

    protected function normalizeForMatch(?string $value): string
    {
        $normalized = Str::ascii((string) $value);
        $normalized = Str::lower($normalized);
        $normalized = preg_replace('/[^a-z0-9\s]/', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim((string) $normalized);
    }

    protected function scoreMatch(string $left, string $right): int
    {
        if ($left === '' || $right === '') {
            return 0;
        }

        if ($left === $right) {
            return 100;
        }

        if (Str::contains($left, $right) || Str::contains($right, $left)) {
            return 92;
        }

        similar_text($left, $right, $similarity);

        $leftTokens = collect(explode(' ', $left))->filter(function ($token) {
            return strlen($token) > 2;
        });
        $rightTokens = collect(explode(' ', $right))->filter(function ($token) {
            return strlen($token) > 2;
        });

        $tokenScore = 0;
        if ($leftTokens->isNotEmpty() && $rightTokens->isNotEmpty()) {
            $tokenScore = (int) round(
                ($leftTokens->intersect($rightTokens)->count() / max($leftTokens->count(), $rightTokens->count())) * 100
            );
        }

        return max((int) round($similarity), $tokenScore);
    }

    protected function extractNumericDocumentNumber(?string $value, int $fallback): int
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if ($digits === '') {
            return $fallback;
        }

        $digits = substr($digits, -18);
        $number = (int) $digits;

        return $number > 0 ? $number : $fallback;
    }
}
