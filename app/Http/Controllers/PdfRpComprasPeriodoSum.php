<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Inventoryhead;
use App\ClienteProveedor;
use App\Documento;
use App\Warehouse;

class PdfRpComprasPeriodoSum extends BaseController
{
    public $registros, $desde, $hasta, $bodega;
    protected $pdf;

    public function __construct(\App\RpCompraSum $pdf)
    {
        $this->pdf = $pdf;
    }

    public function PDFCompraPeriodoZ($desde, $hasta, $bod)
    {
        // Asignamos variables para usar en el reporte
        $this->pdf->desde    = $desde;
        $this->pdf->hasta    = $hasta;

        // Obtenemos la bodega
        $warehouse = Warehouse::find($bod);
        // Si la bodega no existe, puedes manejarlo con un if:
        // if (!$warehouse) { /* Manejar error */ }

        $this->pdf->labodega = $warehouse ? $warehouse->nombre : 'BODEGA DESCONOCIDA';

        $this->bodega = $bod;
        $this->desde  = $desde;
        $this->hasta  = $hasta;

        // Configuramos el PDF
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("SumarizadoCompra");

        $this->contenido();
        $this->pdf->Output();
        exit;
    }

    public function contenido()
    {
        // Obtenemos los detalles
        $detalle = Inventoryhead::leftJoin('cliente_proveedores as pr', 'pr.id', '=', 'inventoryhead.sujeto_id')
            ->leftJoin('documentos as dc', 'dc.id', 'inventoryhead.tpdocufiscal')
            ->select(
                'inventoryhead.*',
                'pr.razon as razon',
                'dc.documento as documento'
            )
            ->where('inventoryhead.tipodoc', 'CPRA')
            ->where('inventoryhead.reversado', 'NO')
            ->where('inventoryhead.bodega', $this->bodega)
            ->whereDate('inventoryhead.fecha', '>=', $this->desde)
            ->whereDate('inventoryhead.fecha', '<=', $this->hasta)
            ->orderBy('fecha')
            ->get();

        // Si no hay datos, podrías manejarlo de varias maneras:
        if ($detalle->isEmpty()) {
            // Podemos poner un mensaje o simplemente salir:
            $this->pdf->SetFont('Times', 'B', 10);
            $this->pdf->Cell(0, 10, 'NO EXISTEN REGISTROS PARA EL PERIODO SELECCIONADO.', 0, 1, 'C');
            return;
        }

        // Si sí hay datos, procedemos:
        $tt  = 0;
        $r   = 1;
        $t1  = 0; // gravado por fecha
        $t2  = 0; // iva por fecha
        $t3  = 0; // total por fecha
        $tt1 = 0; // gravado acumulado total
        $tt2 = 0; // iva acumulado total
        $tt3 = 0; // total acumulado total

        // Obtenemos la fecha del primer registro para iniciar comparaciones
        $fe = $detalle[0]->fecha;  // <-- ya no dará error, porque hemos comprobado que $detalle no está vacío
        $primero = 1;

        $this->pdf->SetFont('Times', '', 8);

        foreach ($detalle as $det) {
            // Si la fecha cambia, imprimimos subtotal por fecha
            if ($fe != $det->fecha) {
                $this->pdf->SetFont('Times', 'B', 8);
                $this->pdf->setX(12);
                $this->pdf->cell(
                    136,
                    5,
                    utf8_decode("TOTAL OPERACION FECHA:") . date('d-m-Y', strtotime($fe)),
                    1,
                    "",
                    "L"
                );
                $this->pdf->cell(15, 5, number_format($t1, 4), 1, "", "R");
                $this->pdf->cell(15, 5, number_format($t2, 4), 1, "", "R");
                $this->pdf->cell(15, 5, number_format($t3, 2), 1, "", "R");
                $this->pdf->Ln();
                $this->pdf->Ln();
                $this->pdf->SetFont('Times', '', 8);

                // Reiniciamos los subtotales
                $t1 = 0;
                $t2 = 0;
                $t3 = 0;

                // Actualizamos $fe a la nueva fecha
                $fe = $det->fecha;
                $primero++;
            }

            // Imprimimos línea del detalle
            $this->pdf->cell(8, 5, $r, 0, "", "C");
            $this->pdf->cell(15, 5, date('d-m-Y', strtotime($det->fecha)), 0, "", "C");
            $this->pdf->cell(80, 5, utf8_decode($det->razon), 0, "", "L");
            $this->pdf->cell(35, 5, utf8_decode($det->documento . " " . $det->numdocufiscal), 0, "", "C");
            $this->pdf->cell(15, 5, number_format($det->gravado, 4), 0, "", "R");
            $this->pdf->cell(15, 5, number_format($det->iva, 4), 0, "", "R");
            $this->pdf->cell(15, 5, number_format($det->total, 2), 0, "", "R");
            $this->pdf->Ln();

            // Acumulamos subtotales
            $t1  += $det->gravado;
            $t2  += $det->iva;
            $t3  += $det->total;

            // Acumulamos totales generales
            $tt1 += $det->gravado;
            $tt2 += $det->iva;
            $tt3 += $det->total;

            $r++;
        }

        // Al terminar el foreach, imprimimos el último subtotal
        $this->pdf->SetFont('Times', 'B', 8);
        $this->pdf->setX(12);
        $this->pdf->cell(
            136,
            5,
            utf8_decode("TOTAL OPERACION FECHA:") . date('d-m-Y', strtotime($fe)),
            1,
            "",
            "L"
        );
        $this->pdf->cell(15, 5, number_format($t1, 4), 1, "", "R");
        $this->pdf->cell(15, 5, number_format($t2, 4), 1, "", "R");
        $this->pdf->cell(15, 5, number_format($t3, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();

        // Finalmente, imprimimos el TOTAL DEL PERIODO
        $this->pdf->setX(12);
        $this->pdf->cell(136, 5, utf8_decode("TOTAL DEL PERIODO:"), 1, "", "L");
        $this->pdf->cell(15, 5, number_format($tt1, 4), 1, "", "R");
        $this->pdf->cell(15, 5, number_format($tt2, 4), 1, "", "R");
        $this->pdf->cell(15, 5, number_format($tt3, 2), 1, "", "R");
        $this->pdf->Ln();

        $this->pdf->SetFont('Times', '', 8);
    }
}
