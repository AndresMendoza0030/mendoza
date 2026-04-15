<?php

namespace App\Http\Controllers;

use App\InventoryAlert;
use App\InventoryAnalytics;
use Illuminate\Http\Request;

class PdfAlertasAbastecimiento extends Controller
{
    public function PDFAlertas(Request $request)
    {
        $alertas = new InventoryAlert(new InventoryAnalytics('movdetails'));

        $config = $alertas->normalizeConfig($request->all());
        $rows = $alertas->getAlertRows($config);
        $summary = $alertas->buildSummary($rows);
        $bodegaPrincipal = $alertas->getPrimaryWarehouse();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reportes.alertas_abastecimiento', [
            'rows' => $rows,
            'summary' => $summary,
            'config' => $config,
            'bodegaPrincipal' => $bodegaPrincipal,
            'generatedAt' => now(),
        ])->setPaper('letter', 'portrait');

        return $pdf->stream('alertas_abastecimiento_' . now()->format('Ymd_His') . '.pdf');
    }
}