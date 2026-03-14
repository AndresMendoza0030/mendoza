<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Fpdf;
use Storage;

use App\Inventoryhead;
use App\Product;
use App\ClienteProveedor;
use App\Movdetail;
use App\Documento;
use App\Warehouse;
use App\Destino;

class PdfRpConsumoPeriodoDet extends BaseController
{
    public  $registros, $desde, $hasta;
    protected $pdf;

    public function __construct(\App\RpConsumoDet $pdf)
    {
        $this->pdf = $pdf;
    }

    public function PDFConsumoPeriodoX($desde, $hasta, $bod)
    {
        $this->pdf->desde    = $desde;
        $this->pdf->hasta    = $hasta;
        $this->desde         = $desde;
        $this->hasta         = $hasta;

        $warehouse = Warehouse::find($bod);
        $this->pdf->labodega = $warehouse ? $warehouse->nombre : 'BODEGA DESCONOCIDA';
        $this->bodega        = $bod;

        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("Detallado-Consumo");

        $this->contenido();
        $this->pdf->Output();
        exit;
    }

    public function PDFConsumoPeriodoXS($tp, $desde, $hasta, $bod)
    {
        $this->pdf->desde    = $desde;
        $this->pdf->hasta    = $hasta;
        $this->desde         = $desde;
        $this->hasta         = $hasta;

        if ($tp != '') {
            $midestino = auth()->user()->destino_pred;
            if ($midestino > 0) {
                $dest = Destino::find($midestino);
                $this->pdf->eltipo = $dest ? (" DESTINO: " . $dest->nombre) : '';
            } else {
                $this->pdf->eltipo = '';
            }
        }

        if (intval($bod) > 0) {
            $warehouse = Warehouse::find($bod);
            $this->pdf->labodega = $warehouse ? $warehouse->nombre : 'BODEGA DESCONOCIDA';
            $this->bodega = $bod;
        } else {
            $this->pdf->labodega = "TODAS LA BODEGAS";
            $this->bodega = 0;
        }

        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("Detallado-Consumo");

        $this->contenido2();
        $this->pdf->Output();
        exit;
    }

    public function contenido()
    {
        // Consulta principal
        $detalle = Movdetail::leftJoin('inventoryhead as ih', 'ih.id', '=', 'movdetails.numerodoc')
            ->leftJoin('productclasses as cl', 'cl.id', 'movdetails.clase')
            ->leftJoin('destinos as ds', 'ds.id', 'ih.destinogasto')
            ->select(
                'movdetails.*',
                'ds.nombre as destino',
                'cl.nombre as cclase',
                'ih.destinogasto as destinogasto',
                DB::raw("SUM(movdetails.cantidad) as tcantidad"),
                DB::raw("SUM(movdetails.gravado) as tgravado")
            )
            ->where('movdetails.tipodoc', 'SGAS')
            ->where('ih.reversado', 'NO')
            ->where('movdetails.bodega', $this->bodega)
            ->whereDate('movdetails.fecha', '>=', $this->desde)
            ->whereDate('movdetails.fecha', '<=', $this->hasta)
            ->groupBy('movdetails.producto_id')
            ->orderBy('ih.destinogasto')
            ->orderBy('movdetails.clase')
            ->orderBy('movdetails.producto_id')
            ->get();

        // Verificamos si $detalle está vacío
        if ($detalle->isEmpty()) {
            // Podrías mostrar un mensaje o simplemente imprimir algo básico
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Cell(0, 10, 'NO EXISTEN REGISTROS PARA EL PERIODO SELECCIONADO.', 0, 1, 'C');
            return;
        }

        $t1  = 0;
        $tt1 = 0;
        $primero = 1;

        // Tomamos el primer destino para inicializar
        $ds  = $detalle[0]->destinogasto;
        $this->pdf->SetFont('Arial', '', 8);

        foreach ($detalle as $det) {
            // Cuando cambia el destino
            if ($ds != $det->destinogasto || $primero == 1) {
                if ($primero != 1) {
                    // Imprimimos subtotal del destino anterior
                    $this->pdf->cell(160, 5, utf8_decode("TOTAL CONSUMO X DESTINO"), 1, "", "L");
                    $this->pdf->cell(20, 5, number_format($t1, 2), 1, "", "R");
                    $this->pdf->Ln();
                    $this->pdf->Ln();
                    // Reiniciamos
                    $t1 = 0;
                }
                // Nuevo bloque de destino
                $this->pdf->SetFont('Arial', 'B', 9);
                $this->pdf->setX(10);
                $this->pdf->cell(60, 6, utf8_decode("DESTINO: " . trim($det->destino)), 0, "", "L");
                $this->pdf->Ln();
                $this->pdf->SetFont('Arial', '', 8);

                // Encabezado de tabla
                $this->pdf->cell(60, 5, "CLASE DE PRODUCTO", 1, "", "C");
                $this->pdf->cell(80, 5, "PRODUCTO", 1, "", "C");
                $this->pdf->cell(20, 5, "CANTIDAD", 1, "", "C");
                $this->pdf->cell(20, 5, "GRAVADO", 1, "", "C");
                $this->pdf->Ln();

                $ds = $det->destinogasto;
                $primero = 0;
            }

            // Imprimimos línea de detalle
            $this->pdf->cell(60, 5, utf8_decode($det->cclase), 0, "", "L");
            $this->pdf->cell(80, 5, substr(utf8_decode($det->producto), 0, 68), 0, "", "L");
            $this->pdf->cell(20, 5, number_format($det->tcantidad, 0), 0, "", "R");
            $this->pdf->cell(20, 5, number_format($det->tgravado, 2), 0, "", "R");
            $this->pdf->Ln();

            // Subtotales
            $t1  += $det->tgravado;
            $tt1 += $det->tgravado;
        }

        // Ultimo subtotal
        $this->pdf->cell(160, 5, utf8_decode("TOTAL CONSUMO X DESTINO"), 1, "", "L");
        $this->pdf->cell(20, 5, number_format($t1, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();

        // TOTAL periodo
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->cell(160, 5, utf8_decode("TOTAL CONSUMO PERIODO"), 1, "", "L");
        $this->pdf->cell(20, 5, number_format($tt1, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 8);
    }

    public function contenido2()
    {
        $midestino = auth()->user()->destino_pred;

        // Construimos la consulta
        if (intval($this->bodega) != 0) {
            $detalle = Movdetail::leftJoin('inventoryhead as ih', 'ih.id', '=', 'movdetails.numerodoc')
                ->leftJoin('productclasses as cl', 'cl.id', 'movdetails.clase')
                ->leftJoin('destinos as ds', 'ds.id', 'ih.destinogasto')
                ->leftJoin('warehouses as w', 'w.id', 'ih.bodega')
                ->select(
                    'movdetails.*',
                    'ds.nombre as destino',
                    'cl.nombre as cclase',
                    'w.nombre as wnombre',
                    DB::raw("SUM(movdetails.gravado) as tgravado"),
                    DB::raw("SUM(movdetails.cantidad) as tcantidad")
                )
                ->where('movdetails.tipodoc', 'SGAS')
                ->where('ih.reversado', 'NO')
                ->where('movdetails.bodega', $this->bodega)
                ->where('ih.destinogasto', $midestino)
                ->whereDate('movdetails.fecha', '>=', $this->desde)
                ->whereDate('movdetails.fecha', '<=', $this->hasta)
                ->groupBy('ih.bodega', 'ih.destinogasto', 'movdetails.clase', 'movdetails.producto_id')
                ->orderBy('ih.bodega')
                ->orderBy('ih.destinogasto')
                ->orderBy('movdetails.clase')
                ->orderBy('movdetails.producto_id')
                ->get();
        } else {
            $detalle = Movdetail::leftJoin('inventoryhead as ih', 'ih.id', '=', 'movdetails.numerodoc')
                ->leftJoin('productclasses as cl', 'cl.id', 'movdetails.clase')
                ->leftJoin('destinos as ds', 'ds.id', 'ih.destinogasto')
                ->leftJoin('warehouses as w', 'w.id', 'ih.bodega')
                ->select(
                    'movdetails.*',
                    'ds.nombre as destino',
                    'cl.nombre as cclase',
                    'w.nombre as wnombre',
                    DB::raw("SUM(movdetails.gravado) as tgravado"),
                    DB::raw("SUM(movdetails.cantidad) as tcantidad")
                )
                ->where('movdetails.tipodoc', 'SGAS')
                ->where('ih.reversado', 'NO')
                ->where('ih.destinogasto', $midestino)
                ->whereDate('movdetails.fecha', '>=', $this->desde)
                ->whereDate('movdetails.fecha', '<=', $this->hasta)
                ->groupBy('ih.bodega', 'ih.destinogasto', 'movdetails.clase', 'movdetails.producto_id')
                ->orderBy('ih.bodega')
                ->orderBy('ih.destinogasto')
                ->orderBy('movdetails.clase')
                ->orderBy('movdetails.producto_id')
                ->get();
        }

        // Verificamos si la consulta está vacía
        if ($detalle->isEmpty()) {
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Cell(0, 10, 'NO EXISTEN REGISTROS PARA EL PERIODO SELECCIONADO.', 0, 1, 'C');
            return;
        }

        $t1  = 0;
        $tt1 = 0;
        $primero = 1;

        // Tomamos la primera bodega para iniciar
        $bd = $detalle[0]->bodega;

        foreach ($detalle as $det) {
            if ($bd != $det->bodega || $primero == 1) {
                if ($primero != 1) {
                    // Subtotal bodega anterior
                    $this->pdf->cell(160, 5, utf8_decode("TOTAL CONSUMO X BODEGA"), 1, "", "L");
                    $this->pdf->cell(20, 5, number_format($t1, 2), 1, "", "R");
                    $this->pdf->Ln();
                    $this->pdf->Ln();
                    $t1 = 0;
                }

                $this->pdf->SetFont('Arial', 'B', 9);
                $this->pdf->setX(10);
                $this->pdf->cell(60, 6, utf8_decode("BODEGA: " . trim($det->wnombre)), 0, "", "L");
                $this->pdf->Ln();
                $this->pdf->SetFont('Arial', '', 8);

                // Encabezados
                $this->pdf->cell(60, 5, "CLASE DE PRODUCTO", 1, "", "C");
                $this->pdf->cell(80, 5, "PRODUCTO", 1, "", "C");
                $this->pdf->cell(20, 5, "CANTIDAD", 1, "", "C");
                $this->pdf->cell(20, 5, "GRAVADO", 1, "", "C");
                $this->pdf->Ln();

                $bd = $det->bodega;
                $primero = 0;
            }

            // Detalle de la fila
            $this->pdf->cell(60, 5, utf8_decode($det->cclase), 0, "", "L");
            $this->pdf->cell(80, 5, substr(utf8_decode($det->producto), 0, 68), 0, "", "L");
            $this->pdf->cell(20, 5, number_format($det->tcantidad, 0), 0, "", "C");
            $this->pdf->cell(20, 5, number_format($det->tgravado, 2), 0, "", "R");
            $this->pdf->Ln();

            $t1  += $det->tgravado;
            $tt1 += $det->tgravado;
        }

        // Ultimo subtotal
        $this->pdf->cell(160, 5, utf8_decode("TOTAL CONSUMO X BODEGA"), 1, "", "L");
        $this->pdf->cell(20, 5, number_format($t1, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();

        // Total período
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->cell(160, 5, utf8_decode("TOTAL CONSUMO PERIODO"), 1, "", "L");
        $this->pdf->cell(20, 5, number_format($tt1, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 8);
    }
}
