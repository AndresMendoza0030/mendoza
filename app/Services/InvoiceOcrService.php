<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceOcrService
{
    public function processImage($imagePath)
    {
        Log::info('OCR processImage iniciado', [
            'imagePath' => $imagePath,
            'exists' => !empty($imagePath) ? file_exists($imagePath) : false,
        ]);

        if (empty($imagePath) || !file_exists($imagePath)) {
            Log::warning('OCR abortado: ruta vacía o archivo inexistente', [
                'imagePath' => $imagePath,
            ]);

            return [
                'raw_text' => '',
                'header' => [],
                'items' => [],
                'totals' => [],
            ];
        }

        $rawText = $this->runTesseract($imagePath);

        Log::info('OCR texto crudo obtenido', [
            'length' => strlen($rawText),
            'preview' => substr($rawText, 0, 1000),
        ]);

        $cleanText = $this->normalizeText($rawText);

        $result = [
            'raw_text' => $cleanText,
            'header' => $this->extractHeader($cleanText),
            'items' => $this->extractItems($cleanText),
            'totals' => $this->extractTotals($cleanText),
        ];

        Log::info('OCR resultado parseado', [
            'header' => $result['header'],
            'items_count' => count($result['items']),
            'totals' => $result['totals'],
        ]);

        return $result;
    }

  protected function runTesseract($imagePath)
{
    if (empty($imagePath) || !file_exists($imagePath)) {
        return '';
    }

    $tesseractPath = '"C:\\Program Files\\Tesseract-OCR\\tesseract.exe"';
    $tessdataDir = '"C:\\Program Files\\Tesseract-OCR\\tessdata"';
    $outputBase = storage_path('app/temp_ocr_' . uniqid());

    $cmd = $tesseractPath
        . ' --tessdata-dir ' . $tessdataDir
        . ' ' . escapeshellarg($imagePath)
        . ' ' . escapeshellarg($outputBase)
        . ' -l spa 2>&1';

    $output = [];
    $returnVar = null;

    exec($cmd, $output, $returnVar);

    \Log::info('Resultado exec Tesseract', [
        'cmd' => $cmd,
        'returnVar' => $returnVar,
        'output' => $output,
    ]);

    $txtPath = $outputBase . '.txt';

    if (!file_exists($txtPath)) {
        return '';
    }

    $text = file_get_contents($txtPath);
    @unlink($txtPath);

    return $text ? $text : '';
}

    protected function normalizeText($text)
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{2,}/', "\n", $text);

        return trim($text);
    }

    protected function extractHeader($text)
    {
        return [
            'supplier_name' => $this->extractSupplierName($text),
            'document_number' => $this->matchPattern($text, '/(DTE[-A-Z0-9]+)/iu'),
            'control_number' => $this->matchPattern($text, '/(?:N[úu]mero de control|Numero de control)\s*:?\s*([A-Z0-9\-]+)/iu'),
            'generation_code' => $this->matchPattern($text, '/(?:C[óo]digo de generaci[óo]n|Codigo de generacion)\s*:?\s*([A-F0-9\-]{20,})/iu'),
            'issue_date' => $this->extractDate($text),
            'payment_terms' => $this->matchPattern($text, '/(?:Condici[óo]n de la operaci[óo]n|COND\.PAGO|Condiciones de pago)\s*:?\s*([A-Z0-9\s\.\-]+)/iu'),
        ];
    }

    protected function extractTotals($text)
    {
        return [
            'total' => $this->extractMoneyNear($text, ['TOTAL A PAGAR', 'Total a Pagar', 'Monto total de la operación', 'Monto total de operacion']),
            'subtotal' => $this->extractMoneyNear($text, ['SUBTOTAL', 'Subtotal']),
            'iva' => $this->extractMoneyNear($text, ['IVA', 'Impuesto al Valor Agregado']),
        ];
    }

    protected function extractItems($text)
    {
        $lines = preg_split('/\n/', $text);
        $items = [];

        Log::info('Líneas OCR para parseo', [
            'lines_count' => count($lines),
            'sample_lines' => array_slice($lines, 0, 25),
        ]);

        foreach ($lines as $line) {
            $normalized = trim(preg_replace('/\s+/', ' ', $line));

            if (mb_strlen($normalized) < 8) {
                continue;
            }

            if ($this->looksLikeTotalLine($normalized)) {
                continue;
            }

            if (
                preg_match('/^(\d+(?:\.\d+)?)\s+(.+?)\s+(\d+(?:\.\d{2,4})?)\s*$/u', $normalized, $m)
                || preg_match('/^([A-Z0-9.\-\/]+)?\s*(\d+(?:\.\d+)?)\s+(.+?)\s+(\d+(?:\.\d{2,4})?)\s*$/u', $normalized, $m)
            ) {
                $qty = null;
                $description = null;
                $unitPrice = null;
                $code = null;

                if (count($m) === 4) {
                    $qty = (float) $m[1];
                    $description = trim($m[2]);
                    $unitPrice = (float) $m[3];
                }

                if (count($m) === 5) {
                    $code = trim((string) $m[1]) ?: null;
                    $qty = (float) $m[2];
                    $description = trim($m[3]);
                    $unitPrice = (float) $m[4];
                }

                if ($description) {
                    $items[] = [
                        'line_no' => count($items) + 1,
                        'code' => $code,
                        'description' => $description,
                        'unit' => null,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => null,
                    ];
                }
            }
        }

        return $items;
    }

    protected function extractSupplierName($text)
    {
        $patterns = [
            '/(?:INFORMACION DEL EMISOR|Emisor)\s+([A-Z0-9\s\.,\-\&]+S\.A\. DE C\.V\.)/iu',
            '/([A-Z0-9\s\.,\-\&]+S\.A\. DE C\.V\.)/iu',
            '/(SIEMENS HEALTHCARE,? ?S\.A\.?)/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return trim($m[1]);
            }
        }

        return null;
    }

    protected function extractDate($text)
    {
        $patterns = [
            '/(\d{4}-\d{2}-\d{2})/',
            '/(\d{2}\/\d{2}\/\d{4})/',
            '/(\d{2}\.\d{2}\.\d{4})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                try {
                    $date = str_replace('.', '/', $m[1]);
                    return Carbon::parse($date)->format('Y-m-d');
                } catch (\Throwable $e) {
                    Log::warning('No se pudo parsear fecha OCR', [
                        'value' => $m[1],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return null;
    }

    protected function looksLikeTotalLine($line)
    {
        $badWords = [
            'TOTAL', 'SUBTOTAL', 'IVA', 'VENTAS', 'GRAVADAS', 'EXENTAS', 'SUJETAS', 'DESCUENTO',
            'RETENCION', 'PAGAR', 'OPERACION', 'SUMA'
        ];

        foreach ($badWords as $word) {
            if (stripos($line, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function matchPattern($text, $pattern)
    {
        return preg_match($pattern, $text, $m) ? trim($m[1]) : null;
    }

    protected function extractMoneyNear($text, array $labels)
    {
        foreach ($labels as $label) {
            $pattern = '/' . preg_quote($label, '/') . '.*?(\d+(?:[\.,]\d{2})?)/isu';
            if (preg_match($pattern, $text, $m)) {
                return (float) str_replace(',', '.', $m[1]);
            }
        }

        return null;
    }
}