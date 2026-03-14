<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use PDF;
use Storage;

use Xarenisoft\NumberToWords\Esp\NumeroALetras;

use App\Comprasbook;
use App\Producto;
use App\Warehouse;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Paciente;
use App\Referente;
use App\Room;
use App\Admongp;
use App\Productsstocks;
use App\Productclassinvoice;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Movdetail;
use App\Generalsetup;
use App\Invoice;
use App\Documento;
use App\Abono;
use App\Invoicedetail;
use App\Vencimiento;
use App\Ingmedico;
use App\Destino;
use App\Pagomedhead;
use App\rxInvoice;
use App\rxMovdetail;
use App\VenceFisico;
use App\Honorario;
//
use App\Conteo;
use App\Fisico;
use App\User;
//


class Pdfcontroller extends Model
{



    public function PDFdetalleMovimientoX($idmov)
    {
        $empresa = Generalsetup::first();
        $elmov = Inventoryhead::find($idmov);
        $labodega = Warehouse::find($elmov->bodega);
        $eldocumento = Documento::where('documento', '=', $elmov->tipodoc)
            ->select('documentos.*')->first();

        $lafecha = $elmov->fecha;
        $elconcepto = $elmov->concepto;
        $labodega = $labodega->nombre;
        $eldocumento = $eldocumento->nombre;

        if ($elmov->tipodoc == "STRA") {
            $labodegadestino = Warehouse::find($elmov->bodegadestino);
            $labodegadestino = $labodegadestino->nombre;
        }

        $detamov = Movdetail::leftjoin('documentos  as dc', 'dc.documento', 'movdetails.tipodoc')
            ->leftjoin('warehouses  as bd', 'bd.id', 'movdetails.bodega')
            ->leftjoin('users  as us', 'us.id', 'movdetails.user_id')
            ->select(
                "movdetails.*",
                'dc.nombre as cdocumento',
                'bd.nombre as cbodega',
                'us.name as nusuario'
            )
            ->where('movdetails.numerodoc', '=', $idmov)->get();

        //construccion de reporte//
        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont("Arial", "B", 11);
        $pdf->Cell(0, 6, $empresa->empresa, 0, "", "C");
        $pdf->Ln();
        $pdf->Cell(0, 6, "Detalle de Movimiento de Inventario", 0, "", "C");
        $pdf->Ln();

        $pdf->SetFont("Arial", "B", 8);
        $pdf->cell(20, 5, "No.Movimiento", 1, "", "C");
        $pdf->cell(70, 5, "Tipo Movimiento", 1, "", "C");
        $pdf->cell(65, 5, "Bodega", 1, "", "C");
        $pdf->cell(35, 5, "Fecha", 1, "", "C");
        $pdf->Ln();
        $pdf->SetFont("Arial", "", 9);
        $pdf->cell(20, 5, $elmov->id, 1, "", "C");
        $pdf->cell(70, 5, $eldocumento, 1, "", "L");
        $pdf->cell(65, 5, $labodega, 1, "", "L");
        $pdf->cell(35, 5, $elmov->fecha, 1, "", "C");
        $pdf->Ln();
        $pdf->Ln();

        //
        if ($elmov->tipodoc == "STRA") {
            $pdf->SetFont("Arial", "B", 8);
            $pdf->cell(20, 5, "No.Movimiento", 1, "", "C");
            $pdf->cell(70, 5, "Tipo Movimiento", 1, "", "C");
            $pdf->cell(65, 5, "Bodega", 1, "", "C");
            $pdf->Ln();
            $pdf->SetFont("Arial", "", 9);
            $pdf->cell(20, 5, $elmov->id, 1, "", "C");
            $pdf->cell(70, 5, "ENTRADA X TRASLADO", 1, "", "L");
            $pdf->cell(65, 5, $labodegadestino, 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }
        if ($elmov->tipodoc == "CPRA") {
            $pdf->SetFont("Arial", "B", 8);
            $pdf->cell(97, 5, "PROVEEDOR", 1, "", "C");
            $pdf->cell(93, 5, "REFERENCIA", 1, "", "C");
            $pdf->Ln();
            $pdf->SetFont("Arial", "", 9);
            $pdf->cell(97, 5, $elmov->sujeto, 1, "", "C");
            $pdf->cell(93, 5, $elmov->tpdocufiscal . '-' . $elmov->numdocufiscal, 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }

        //inicio tabla encabezados
        $tt = 0;
        $pdf->SetFont("Arial", "B", 8);
        if ($elmov->tipodoc == "CPRA") {
            $pdf->cell(20, 5, "Cantidad", 1, "", "C");
            $pdf->cell(125, 5, "Producto/Servicio", 1, "", "C");
            $pdf->cell(20, 5, "CostoUnit", 1, "", "C");
            $pdf->cell(25, 5, "Monto Costo", 1, "", "C");
            $pdf->Ln();
        } else {
            $pdf->cell(50, 5, "Cantidad", 1, "", "C");
            $pdf->cell(140, 5, "Producto/Servicio", 1, "", "C");
            $pdf->Ln();
        }

        foreach ($detamov as $dt) {
            if ($elmov->tipodoc == "CPRA") {
                $pdf->cell(20, 5, $dt->cantidad, 0, "", "C");
                $pdf->cell(125, 5, utf8_decode($dt->producto), 0, "", "L");
                $pdf->cell(20, 5, $dt->precosto, 0, "", "R");
                $pdf->cell(25, 5, number_format($dt->total, 2, ".", ","), 0, "", "R");
                $pdf->Ln();
            } else {
                $pdf->cell(50, 5, $dt->cantidad, 0, "", "C");
                $pdf->cell(140, 5, utf8_decode($dt->producto), 0, "", "L");
                $pdf->Ln();
            }
            $tt = $tt + $dt->total;
        }
        if ($elmov->tipodoc == "CPRA") {
            $pdf->cell(165, 5, "Total x Documento", 0, "", "R");
            $pdf->cell(25, 5, number_format($tt, 2, ".", ","), "T", "", "R");
            $pdf->Ln();
        }

        $pdf->Output();
        exit;
        //fin de reporte
        //envio a pdf
    }



    public function PDFdetalleMovimiento($idmov)
    {
        $empresa = Generalsetup::first();
        $elmov = Inventoryhead::find($idmov);

        // Obtener destino (si aplica)
        $cdestino = null;
        if ($elmov->destinogasto) {
            $cdestino = \App\Destino::find($elmov->destinogasto);
        }

        // Obtener bodega y documento
        $bodegaObj = \App\Warehouse::find($elmov->bodega);
        $labodega = $bodegaObj ? $bodegaObj->nombre : '';
        $documentoObj = Documento::where('documento', '=', $elmov->tipodoc)
            ->select('documentos.*')->first();
        $eldocumento = $documentoObj ? $documentoObj->nombre : '';

        // Si es traslado, obtener bodega destino
        if ($elmov->tipodoc == "STRA") {
            $bdestinoObj = \App\Warehouse::find($elmov->bodegadestino);
            $labodegadestino = $bdestinoObj ? $bdestinoObj->nombre : '';
        }

        $lafecha = $elmov->fecha;
        $elconcepto = $elmov->concepto;

        // Obtener detalles del movimiento
        $detamov = Movdetail::leftJoin('documentos as dc', 'dc.documento', 'movdetails.tipodoc')
            ->leftJoin('vencimientos as v', 'v.id', 'movdetails.lotevence')
            ->leftJoin('warehouses as bd', 'bd.id', 'movdetails.bodega')
            ->leftJoin('users as us', 'us.id', 'movdetails.user_id')
            ->select(
                "movdetails.*",
                "dc.nombre as cdocumento",
                "bd.nombre as cbodega",
                "us.name as nusuario",
                "v.mes as mes",
                "v.anno as anno"
            )
            ->where('movdetails.numerodoc', '=', $idmov)
            ->where('movdetails.tipodoc', '=', $elmov->tipodoc)
            ->get();

        // Construcción del reporte PDF
        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        // Se establece orientación vertical (Portrait) y tamaño Letter
        $pdf->AddPage('P', 'Letter');
        $pdf->SetFont("Courier", "B", 10);

        // Encabezado: Número de página y nombre de la empresa
        $pdf->SetFont('Courier', 'I', 7);
        $pdf->SetX(1);
        $pdf->Cell(0, 5, 'Pg.' . $pdf->PageNo() . '/{nb}', 0, 1, 'L');
        $pdf->SetFont('Courier', 'I', 8);
        $pdf->SetX(5);
        $pdf->Cell(0, 6, $empresa->empresa, 0, 1, 'C');
        $pdf->Ln();
        $pdf->Cell(0, 6, "Detalle de Movimiento de Inventario", 0, 1, 'C');
        $pdf->Ln();

        // Encabezado de la cabecera del movimiento (solo una copia)
        $pdf->SetFillColor(214, 219, 223);
        $pdf->SetFont("Courier", "B", 8);
        $pdf->SetX(2);
        // Agregamos columna extra para "Autorizado"
        if ($elmov->tipodoc == "CPRA") {
            $pdf->Cell(20, 5, "Cnt/Lote", 1, 0, "C", true);
            $pdf->Cell(81, 5, "Producto/Servicio", 1, 0, "C", true);
            $pdf->Cell(17, 5, "Costo U.", 1, 0, "C", true);
            $pdf->Cell(17, 5, "Mto Costo", 1, 0, "C", true);
            $pdf->Cell(20, 5, "Autorizado", 1, 1, "C", true);
        } else {
            $pdf->Cell(35, 5, "Cantidad / Vcto", 1, 0, "C", true);
            $pdf->Cell(100, 5, "Producto/Servicio", 1, 0, "C", true);
            $pdf->Cell(20, 5, "Autorizado", 1, 1, "C", true);
        }

        // Imprimir datos de cabecera (ejemplo para CPRA)
        $pdf->SetFont("Courier", "", 8);
        $pdf->SetX(2);
        if ($elmov->tipodoc == "CPRA") {
            $pdf->Cell(20, 5, $elmov->id, 1, 0, "C");
            $pdf->Cell(81, 5, $eldocumento, 1, 0, "L");
            $pdf->Cell(17, 5, $labodega, 1, 0, "L");
            $pdf->Cell(17, 5, date("d-m-Y", strtotime($elmov->fecha)), 1, 0, "C");
            // Asumimos 0 en Autorizado si no se tiene valor en la cabecera
            $pdf->Cell(20, 5, "0", 1, 1, "R");
        } else {
            $pdf->Cell(35, 5, "", 1, 0, "C");
            $pdf->Cell(100, 5, "", 1, 0, "C");
            $pdf->Cell(20, 5, "", 1, 1, "R");
        }
        $pdf->Ln();

        // Inicializamos el acumulador para totales
        $tt = 0;

        // Imprimir el detalle del movimiento con la nueva columna "Autorizado"
        foreach ($detamov as $dt) {
            if ($elmov->tipodoc == "CPRA") {
                $pdf->SetX(2);
                $pdf->Cell(20, 5, $dt->cantidad . " " . $dt->mes . "/" . $dt->anno, 0, 0, "C");
                $pdf->Cell(81, 5, substr(utf8_decode($dt->producto), 0, 80), 0, 0, "L");
                $pdf->Cell(17, 5, number_format($dt->precosto, 4, ".", ","), 0, 0, "R");
                $pdf->Cell(17, 5, number_format($dt->gravado, 4, ".", ","), 0, 0, "R");
                // Se muestra la cantidad autorizada; si no existe, se muestra 0
                $pdf->Cell(20, 5, number_format($dt->autorizado ?? 0, 4, ".", ","), 0, 1, "R");
            } else {
                $pdf->SetX(2);
                $pdf->Cell(15, 5, $dt->cantidad, 0, 0, "C");
                $pdf->Cell(25, 5, trim($dt->mes) . "/" . trim($dt->anno), 0, 0, "L");
                $pdf->SetX(35);
                $pdf->Cell(100, 5, $dt->producto_id . ' - ' . utf8_decode($dt->producto), 0, 0, "L");
                // Se añade la columna "Autorizado"
                $pdf->Cell(20, 5, number_format($dt->autorizado ?? 0, 4, ".", ","), 0, 1, "R");
                $pdf->Ln();
            }
            $tt += $dt->gravado;
        }

        if ($elmov->tipodoc == "CPRA") {
            $pdf->SetX(97);
            $pdf->Cell(25, 5, "Total x Documento", 0, 0, "R");
            $pdf->Cell(15, 5, number_format($tt, 4, ".", ","), "T", 0, "R");
            $pdf->Ln();
        }
        $pdf->Ln(3);
        $pdf->SetX(2);
        $pdf->Cell(75, 5, "ENTREGADO POR:", "T", 0, "C");
        $pdf->Cell(60, 5, "RECIBIDO POR:", "T", 1, "C");

        $pdf->Output();
        exit;
    }





    public function PDFConteoFisico($idm)
    {
        $head = Conteo::find($idm);
        $local = Generalsetup::first();

        if (!$head) {
            return;
        }
        if ($head) {
            $cbodega  = Warehouse::find($head->bodega_id)->nombre;
            $cusuario = User::find($head->user_id)->name;

            $cargos = Fisico::leftjoin('warehouses as bd', 'bodega_id', '=', 'bd.id')
                ->leftjoin('products as pr', 'producto_id', '=', 'pr.id')
                ->where('conteo_id', '=', $idm)->select(
                    'inv_fisico.*',
                    'bd.nombre as cbodega',
                    DB::raw("CONCAT(TRIM(pr.nombre)) as producto"),
                    DB::raw("pr.costo * diferencia as mtodiferencia")
                )
                ->orderby('tipoajuste')->orderby('pr.nombre')->orderby('pr.presentacion')->get();
        }

        //construccion de reporte//
        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        //
        $pdf->SetFont("Courier", "B", 11);
        $pdf->Cell(0, 6, utf8_decode($local->empresa), 0, "", "C");
        $pdf->Ln();
        $pdf->Cell(0, 6, utf8_decode("Conteo Físico de Inventario en Bodega"), 0, "", "C");
        $pdf->Ln();
        $pdf->Ln();

        // Arial 12
        $pdf->SetFont('Courier', '', 11);
        // Datos
        $pdf->cell(95, 6, utf8_decode("Bodega: " . $cbodega), 1, "", "L");
        $pdf->cell(45, 6, "Fecha " . date("d/m/Y", strtotime($head->fecha)), 1, "", "L");
        $pdf->cell(50, 6, "Conteo No: " . $head->id, 1, "", "L");
        $pdf->Ln();
        $pdf->Ln();
        //encabeza de reporte
        $pdf->SetFont("Courier", "B", 9);
        $pdf->cell(100, 5, "Producto/Servicio", 1, "", "C");
        $pdf->cell(20, 5, "Stk-Sist.", 1, "", "C");
        $pdf->cell(20, 5, "Conteo", 1, "", "C");
        $pdf->cell(20, 5, "Difiere", 1, "", "C");
        $pdf->cell(20, 5, "Mto Dif.", 1, "", "C");
        $pdf->cell(15, 5, "Tp.Aj.", 1, "", "C");
        // Salto de línea
        $pdf->Ln();

        //
        foreach ($cargos as $cargo) {
            $tpaj = "";
            if ($cargo->tipoajuste == "E") {
                $tpaj = "ENTRADA";
            }
            if ($cargo->tipoajuste == "S") {
                $tpaj = "SALIDA";
            }
            $pdf->cell(100, 5, $cargo->producto_id . " - " . substr(utf8_decode($cargo->producto), 0, 45), 0, "", "L");
            $pdf->cell(20, 5, $cargo->actual, 0, "", "C");
            $pdf->cell(20, 5, $cargo->contado, 0, "", "C");
            $pdf->cell(20, 5, $cargo->diferencia, 0, "", "C");
            $pdf->cell(20, 5, number_format($cargo->mtodiferencia, 2), 0, "", "C");
            $pdf->cell(15, 5, $tpaj, 0, "", "L");
            $pdf->Ln();
        }
        $pdf->Output();
        exit;
        //fin de reporte
        //envio a pdf
    }

    public function PDFVenceFisico($idm)
    {
        $head = Conteo::find($idm);
        $local = Generalsetup::first();

        if (!$head) {
            return;
        }
        if ($head) {
            $cbodega  = Warehouse::find($head->bodega_id)->nombre;
            $cusuario = User::find($head->user_id)->name;

            $cargos = VenceFisico::leftjoin('products as pr', 'producto_id', '=', 'pr.id')
                ->where('idconteo', '=', $idm)->select(
                    'vence_fisico.*',
                    DB::raw("CONCAT(TRIM(pr.nombre)) as producto")
                )
                ->orderby('tipomovi')->orderby('pr.nombre')->orderby('pr.presentacion')->get();
        }

        //construccion de reporte//
        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        //
        $pdf->SetFont("Arial", "B", 10);
        $pdf->Cell(0, 6, utf8_decode($local->empresa), 0, "", "C");
        $pdf->Ln();
        $pdf->Cell(0, 6, utf8_decode("Conteo Físico de Vencimientos en Bodega"), 0, "", "C");
        $pdf->Ln();
        $pdf->Ln();

        // Arial 12
        $pdf->SetFont('Arial', '', 10);
        // Datos
        $pdf->cell(95, 6, utf8_decode("Bodega: " . $cbodega), 1, "", "L");
        $pdf->cell(45, 6, "Fecha " . date("d/m/Y", strtotime($head->fecha)), 1, "", "L");
        $pdf->cell(50, 6, "Conteo No: " . $idm, 1, "", "L");
        $pdf->Ln();
        $pdf->Ln();
        //encabeza de reporte
        $pdf->SetFont("Arial", "B", 9);
        $pdf->cell(100, 5, "Producto/Servicio", 1, "", "C");
        $pdf->cell(25, 5, "Vence-Sistema", 1, "", "C");
        $pdf->cell(25, 5, "Vence-Conteo", 1, "", "C");
        $pdf->cell(25, 5, "Diferencia", 1, "", "C");
        $pdf->cell(15, 5, "Tp.Aj.", 1, "", "C");
        // Salto de línea
        $pdf->Ln();

        //
        foreach ($cargos as $cargo) {
            $tpaj = "";
            if ($cargo->tipomovi == "E") {
                $tpaj = "ENTRADA";
            }
            if ($cargo->tipomovi == "S") {
                $tpaj = "SALIDA";
            }
            $pdf->cell(95, 5, $cargo->producto_id . " - " . utf8_decode($cargo->producto), 0, "", "L");
            $pdf->cell(30, 5, $cargo->mes . "/" . $cargo->anno . " : " . $cargo->habia, 0, "", "C");
            $pdf->cell(25, 5, $cargo->conteo, 0, "", "C");
            $pdf->cell(25, 5, $cargo->diferencia, 0, "", "C");
            $pdf->cell(15, 5, $tpaj, 0, "", "L");
            $pdf->Ln();
        }
        $pdf->Output();
        exit;
        //fin de reporte
        //envio a pdf
    }
}
