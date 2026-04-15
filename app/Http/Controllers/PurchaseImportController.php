<?php

namespace App\Http\Controllers;

use App\ClienteProveedor;
use App\Product;
use App\Warehouse;
use App\PurchaseImport;
use App\Services\InvoiceOcrService;
use App\Services\PurchaseImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseImportController extends Controller
{
    public function index()
    {
        $imports = PurchaseImport::latest()->paginate(10);

        return view('purchase_import.index', compact('imports'));
    }

   public function upload(Request $request, InvoiceOcrService $ocrService, PurchaseImportService $importService)
{
    $request->validate([
        'invoice_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
    ]);

    if (!$request->hasFile('invoice_image')) {
        return back()->withErrors(['invoice_image' => 'No se recibió ningún archivo.']);
    }

    $file = $request->file('invoice_image');

    if (!$file || !$file->isValid()) {
        return back()->withErrors(['invoice_image' => 'El archivo no es válido o no terminó de subirse.']);
    }

    $destinationPath = storage_path('app/public/purchase_imports');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0777, true);
    }

    $extension = strtolower($file->getClientOriginalExtension());
    $filename = date('Ymd_His') . '_' . uniqid() . '.' . $extension;

    $file->move($destinationPath, $filename);

    $path = 'purchase_imports/' . $filename;
    $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

    if (!file_exists($fullPath)) {
        return back()->withErrors(['invoice_image' => 'No se pudo mover la imagen al almacenamiento local.']);
    }

    $ocrResult = $ocrService->processImage($fullPath);

    $purchaseImport = $importService->createDraftFromOcr($ocrResult, $path, auth()->id());

    return redirect()->route('purchase-import.review', $purchaseImport->id)
        ->with('success', 'Factura procesada correctamente.');
}

    public function review($id)
    {
        $purchaseImport = PurchaseImport::with('items.product')->findOrFail($id);
        $products = Product::query()
            ->where('tipo', 'PRODUCTO')
            ->where('estatus', '0')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        $suppliers = ClienteProveedor::query()
            ->where('tipo', 'PROVEEDOR')
            ->where('estatus', '0')
            ->orderBy('razon')
            ->get(['id', 'razon', 'nit', 'nrc']);
        $warehouses = Warehouse::query()
            ->where('estatus', '0')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        $selectedSupplierId = $this->guessSupplierId($purchaseImport, $suppliers);
        $selectedWarehouseId = old('warehouse_id', optional($warehouses->first())->id);

        return view('purchase_import.review', compact(
            'purchaseImport',
            'products',
            'suppliers',
            'warehouses',
            'selectedSupplierId',
            'selectedWarehouseId'
        ));
    }

    public function confirm(Request $request, $id, PurchaseImportService $importService)
    {
        $purchaseImport = PurchaseImport::with('items')->findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|integer|exists:cliente_proveedores,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.matched_product_id' => 'nullable|integer',
            'items.*.qty' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $importService->updateReviewData($purchaseImport, $validated['items']);
        $importService->confirmAsPurchaseEntry(
            $purchaseImport,
            (int) $validated['supplier_id'],
            (int) $validated['warehouse_id'],
            Auth::id()
        );

        return redirect()->route('purchase-import.index')
            ->with('success', 'Entrada por compra registrada correctamente.');
    }

    private function guessSupplierId(PurchaseImport $purchaseImport, $suppliers): ?int
    {
        $oldSupplierId = old('supplier_id');
        if ($oldSupplierId) {
            return (int) $oldSupplierId;
        }

        if ($purchaseImport->supplier_nit) {
            $match = $suppliers->firstWhere('nit', $purchaseImport->supplier_nit);
            if ($match) {
                return (int) $match->id;
            }
        }

        if ($purchaseImport->supplier_nrc) {
            $match = $suppliers->firstWhere('nrc', $purchaseImport->supplier_nrc);
            if ($match) {
                return (int) $match->id;
            }
        }

        $needle = Str::lower(trim((string) $purchaseImport->supplier_name));
        if ($needle === '') {
            return null;
        }

        $match = $suppliers->first(function ($supplier) use ($needle) {
            $candidate = Str::lower(trim((string) $supplier->razon));

            return $candidate !== '' && (Str::contains($candidate, $needle) || Str::contains($needle, $candidate));
        });

        return $match ? (int) $match->id : null;
    }
}
