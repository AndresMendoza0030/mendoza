<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Generalsetup;
use Fpdf\Fpdf; // Asegúrate de tener instalado y configurado FPDF (o usar app('Fpdf'))

class PDFGastoController extends Controller
{
    /**
     * Genera un PDF con el reporte de gasto/consumo
     * para un movimiento dado ($idmov) utilizando la tabla detareqs.
     *
     * Muestra: Cantidad Solicitada, Cantidad Autorizada, Producto y Presentación.
     */
    public function generatePDF($idmov)
    {
        // 1. Obtener la información general de la empresa
        $empresa = Generalsetup::first();

        // 2. Obtener los registros de la tabla detareqs para el movimiento
        //    (suponemos que en detareqs el tipodoc es 'PGAS' para gastos)
        $detalles = DB::table('detareqs')
            ->select(
                'cantidad as cantidad_solicitada',
                'autorizado as cantidad_autorizada',
                'producto',
                'presentacion'
            )
            ->where('numerodoc', $idmov)
            ->where('tipodoc', 'PGAS')
            ->get();

        // 3. Crear el objeto FPDF y configurar la página (orientación "P" para vertical)
        //    Puedes ajustar los márgenes, fuentes, etc. según tus necesidades.
        $pdf = new Fpdf();
        $pdf->AddPage('P', 'Letter');  // "P" para Portrait (vertical) y tamaño Letter
        $pdf->SetFont('Arial', 'B', 12);

        // 4. Título del reporte
        $pdf->Cell(0, 10, 'Reporte de Gasto/Consumo', 0, 1, 'C');

        // 5. Nombre de la empresa (si se obtuvo)
        if ($empresa) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 8, 'Empresa: ' . $empresa->empresa, 0, 1, 'C');
        }
        $pdf->Ln(3);

        // 6. Encabezado de la tabla
        $pdf->SetFont('Arial', 'B', 10);
        // Definimos anchos para cada columna:
        // 40 mm para la cantidad solicitada, 40 mm para la cantidad autorizada,
        // 60 mm para el producto y 40 mm para la presentación.
        $pdf->Cell(40, 8, 'Cant. Solicitada', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Cant. Autorizada', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Producto', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Presentación', 1, 1, 'C');  // El 1 en el último parámetro provoca salto de línea

        // 7. Cuerpo de la tabla: se recorren los registros obtenidos
        $pdf->SetFont('Arial', '', 10);
        foreach ($detalles as $d) {
            $pdf->Cell(40, 8, $d->cantidad_solicitada, 1, 0, 'C');
            $pdf->Cell(40, 8, $d->cantidad_autorizada, 1, 0, 'C');
            // Para evitar que el texto se salga de la celda se puede usar substr
            $pdf->Cell(60, 8, substr(utf8_decode($d->producto), 0, 55), 1, 0, 'L');
            $pdf->Cell(40, 8, substr(utf8_decode($d->presentacion), 0, 25), 1, 1, 'L');
        }

        // 8. Generar el PDF para salida (lo puedes forzar a descarga o visualizar en el navegador)
        $pdf->Output();
        exit;
    }
}
