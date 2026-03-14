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
use App\Detareq;
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

        $cdestino = "";
        $cdestino = Destino::find($elmov->destinogasto);

        $labodega = Warehouse::find($elmov->bodega);
        $eldocumento = Documento::where('documento', '=', $elmov->tipodoc)
            ->select('documentos.*')->first();


        if ($elmov->paciente_id > 0) {
            $datospaciente = Paciente::leftjoin('rooms as r', 'r.id', 'pacientes.room')
                ->leftjoin('referentes as md', 'md.id', 'pacientes.referente')
                ->select(
                    "pacientes.*",
                    'r.nombre as elcuarto',
                    DB::raw("CONCAT(TRIM(md.apellidos),' ',TRIM(md.nombres)) as elmedico"),
                    DB::raw("CONCAT(TRIM(pacientes.fecha),' - ',TRIM(pacientes.hora)) as fechayhora"),
                    DB::raw("CONCAT(TRIM(pacientes.nombres),' ',TRIM(pacientes.apellidos)) as nombrepaciente")
                )
                ->where('pacientes.id', '=', $elmov->paciente_id)->first();
        }



        $lafecha = $elmov->fecha;
        $elconcepto = $elmov->concepto;
        $labodega = $labodega->nombre;
        $eldocumento = $eldocumento->nombre;

        if ($elmov->tipodoc == "STRA") {
            $labodegadestino = Warehouse::find($elmov->bodegadestino);
            $labodegadestino = $labodegadestino->nombre;
        }

        $detamov = Movdetail::leftjoin('documentos  as dc', 'dc.documento', 'movdetails.tipodoc')
            ->leftjoin('vencimientos  as v', 'v.id', 'movdetails.lotevence')
            ->leftjoin('warehouses  as bd', 'bd.id', 'movdetails.bodega')
            ->leftjoin('users  as us', 'us.id', 'movdetails.user_id')
            //->leftjoin('detareqs  as dts', 'dts.id', 'detareqs.autorizado')
            ->select(
                "movdetails.*",
                'dc.nombre as cdocumento',
                'bd.nombre as cbodega',
                'us.name as nusuario',
                'v.mes as mes',
                'v.anno as anno'
            )
            ->where('movdetails.numerodoc', '=', $idmov)->where('movdetails.tipodoc', '=', $elmov->tipodoc)->get();


        //construccion de reporte//
        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage('L', 'Letter');
        $pdf->SetFont("Courier", "B", 10);

        // Número de página
        $pdf->SetFont('Courier', 'I', 7);
        $pdf->Setx(1);
        $pdf->Cell(0, 5, 'Pg.' . $pdf->PageNo() . '/{nb}', 0, 0, 'L');
        $pdf->SetFont('Courier', 'I', 8);
        $pdf->Setx(5);
        $pdf->Cell(135, 6, $empresa->empresa, 0, "", "C");

        $pdf->Setx(141);
        // Número de página
        $pdf->SetFont('Courier', 'I', 7);
        $pdf->Cell(0, 5, 'Pg.' . $pdf->PageNo() . '/{nb}', 0, 0, 'L');
        $pdf->SetFont('Courier', 'I', 8);
        $pdf->Setx(142);
        $pdf->Cell(135, 6, $empresa->empresa, 0, "", "C");
        $pdf->Ln();

        $pdf->Cell(135, 6, "Detalle de Movimiento de Inventario", 0, "", "C");
        $pdf->Setx(142);
        $pdf->Cell(135, 6, "Detalle de Movimiento de Inventario", 0, "", "C");
        $pdf->Ln();

        $pdf->SetFillColor(214, 219, 223);

        $pdf->SetFont("Courier", "B", 8);
        $pdf->Setx(2);
        $pdf->cell(12, 5, "No.Mov", 1, "", "C", true);
        $pdf->cell(60, 5, "Tipo Movimiento", 1, "", "C", true);
        $pdf->cell(40, 5, "Bodega", 1, "", "C", true);
        $pdf->cell(23, 5, "Fecha", 1, "", "C", true);
        //
        $pdf->Setx(142);
        //
        $pdf->cell(12, 5, "No.Mov", 1, "", "C", true);
        $pdf->cell(60, 5, "Tipo Movimiento", 1, "", "C", true);
        $pdf->cell(40, 5, "Bodega", 1, "", "C", true);
        $pdf->cell(23, 5, "Fecha", 1, "", "C", true);
        //
        $pdf->Ln();
        $pdf->SetFont("Courier", "", 8);
        $pdf->Setx(2);
        $pdf->cell(12, 5, $elmov->id, 1, "", "C");
        $pdf->cell(60, 5, $eldocumento, 1, "", "L");
        $pdf->cell(40, 5, $labodega, 1, "", "L");
        $pdf->cell(23, 5, date("d-m-Y", strtotime($elmov->fecha)), 1, "", "C");
        //
        $pdf->Setx(142);
        //
        $pdf->cell(12, 5, $elmov->id, 1, "", "C");
        $pdf->cell(60, 5, $eldocumento, 1, "", "L");
        $pdf->cell(40, 5, $labodega, 1, "", "L");
        $pdf->cell(23, 5, date("d-m-Y", strtotime($elmov->fecha)), 1, "", "C");
        $pdf->Ln();

        //
        if ($elmov->tipodoc == "SCAR" || $elmov->tipodoc == "SERV" || $elmov->tipodoc == "DEVO") {
            $pdf->Setx(2);
            $pdf->cell(65, 5, "Nombre Paciente", 1, "", "C", true);
            $pdf->cell(20, 5, "Cuarto", 1, "", "C", true);
            $pdf->cell(50, 5, utf8_decode("Médico"), 1, "", "C", true);
            //
            $pdf->Setx(142);
            //
            $pdf->cell(65, 5, "Nombre Paciente", 1, "", "C", true);
            $pdf->cell(20, 5, "Cuarto", 1, "", "C", true);
            $pdf->cell(50, 5, utf8_decode("Médico"), 1, "", "C", true);
            $pdf->Ln();
            //
            $pdf->Setx(2);
            $pdf->cell(65, 5, $datospaciente->id . ' - ' . $datospaciente->nombrepaciente, 1, "", "L");
            $pdf->cell(20, 5, $datospaciente->elcuarto, 1, "", "C");
            $pdf->cell(50, 5, substr($datospaciente->elmedico, 0, 29), 1, "", "L");
            //
            $pdf->Setx(142);
            //
            $pdf->cell(65, 5, $datospaciente->id . ' - ' . $datospaciente->nombrepaciente, 1, "", "L");
            $pdf->cell(20, 5, $datospaciente->elcuarto, 1, "", "C");
            $pdf->cell(50, 5, substr($datospaciente->elmedico, 0, 29), 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }

        if ($elmov->tipodoc == "STRA") {
            $pdf->SetFont("Courier", "B", 8);
            $pdf->Setx(2);
            $pdf->cell(18, 5, "No.Mov", 1, "", "C", true);
            $pdf->cell(65, 5, "Tipo Movimiento", 1, "", "C", true);
            $pdf->cell(52, 5, "Bodega Destino", 1, "", "C", true);
            //
            $pdf->Setx(142);
            //
            $pdf->cell(18, 5, "No.Mov", 1, "", "C", true);
            $pdf->cell(65, 5, "Tipo Movimiento", 1, "", "C", true);
            $pdf->cell(52, 5, "Bodega Destino", 1, "", "C", true);
            $pdf->Ln();

            $pdf->Setx(2);
            $pdf->cell(18, 5, $elmov->docucontra, 1, "", "C");
            $pdf->cell(65, 5, "ENTRADA X TRASLADO", 1, "", "L");
            $pdf->cell(52, 5, $labodegadestino, 1, "", "L");
            //
            $pdf->Setx(142);
            //
            $pdf->cell(18, 5, $elmov->docucontra, 1, "", "C");
            $pdf->cell(65, 5, "ENTRADA X TRASLADO", 1, "", "L");
            $pdf->cell(52, 5, $labodegadestino, 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }
        if ($elmov->tipodoc == "SGAS") {
            $pdf->Setx(2);
            $pdf->cell(50, 5, "DESTINO", 1, "", "C", true);
            $pdf->cell(85, 5, "CONCEPTO", 1, "", "C", true);
            $pdf->Setx(142);
            $pdf->cell(50, 5, "DESTINO", 1, "", "C", true);
            $pdf->cell(85, 5, "CONCEPTO", 1, "", "C", true);
            $pdf->Ln();
            $pdf->Setx(2);
            $pdf->cell(50, 5, $cdestino->nombre, 1, "", "C");
            $pdf->cell(85, 5, substr($elmov->concepto, 0, 75), 1, "", "L");
            $pdf->Setx(142);
            $pdf->cell(50, 5, $cdestino->nombre, 1, "", "C");
            $pdf->cell(85, 5, substr($elmov->concepto, 0, 75), 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }

        if ($elmov->tipodoc == "EAJU" || $elmov->tipodoc == "SAJU") {
            $pdf->Setx(2);
            $pdf->cell(135, 5, "CONCEPTO", 1, "", "C", true);
            $pdf->Setx(142);
            $pdf->cell(135, 5, "CONCEPTO", 1, "", "C", true);
            $pdf->Ln();
            $pdf->Setx(2);
            $pdf->cell(135, 5, substr($elmov->concepto, 0, 134), 1, "", "L");
            $pdf->Setx(142);
            $pdf->cell(135, 5, substr($elmov->concepto, 0, 134), 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }


        if ($elmov->tipodoc == "CPRA") {
            $pdf->Setx(2);
            $pdf->cell(80, 5, "PROVEEDOR", 1, "", "C", true);
            $pdf->cell(55, 5, "REFERENCIA", 1, "", "C", true);
            $pdf->Setx(142);
            $pdf->cell(80, 5, "PROVEEDOR", 1, "", "C", true);
            $pdf->cell(55, 5, "REFERENCIA", 1, "", "C", true);
            $pdf->Ln();

            $pdf->Setx(2);
            $pdf->cell(80, 5, $elmov->sujeto, 1, "", "C");
            $pdf->cell(55, 5, $elmov->tpdocufiscal . '-' . $elmov->numdocufiscal, 1, "", "L");
            $pdf->Setx(142);
            $pdf->cell(80, 5, $elmov->sujeto, 1, "", "C");
            $pdf->cell(55, 5, $elmov->tpdocufiscal . '-' . $elmov->numdocufiscal, 1, "", "L");
            $pdf->Ln();
            $pdf->Ln();
        }

        //inicio tabla encabezados
        $tt = 0;
        $pdf->SetFont("Arial", "B", 8);
        if ($elmov->tipodoc == "CPRA") {
            $pdf->Setx(2);
            $pdf->cell(20, 5, "Cnt/Lote", 1, "", "C", true);
            $pdf->cell(81, 5, "Producto/Servicio", 1, "", "C", true);
            $pdf->cell(17, 5, "Costo U.", 1, "", "C", true);
            $pdf->cell(17, 5, "Mto Costo", 1, "", "C", true);
            $pdf->Setx(142);
            $pdf->cell(20, 5, "Cnt/Lote", 1, "", "C", true);
            $pdf->cell(81, 5, "Producto/Servicio", 1, "", "C", true);
            $pdf->cell(17, 5, "Costo U.", 1, "", "C", true);
            $pdf->cell(17, 5, "Mto Costo", 1, "", "C", true);
            $pdf->Ln();
        } else {
            $pdf->Setx(2);
            $pdf->cell(35, 5, "Cantidad / Vcto", 1, "", "C", true);
            $pdf->cell(100, 5, "Producto/Servicio", 1, "", "C", true);
            $pdf->Setx(142);
            $pdf->cell(35, 5, "Cantidad / Vcto", 1, "", "C", true);
            $pdf->cell(100, 5, "Producto/Servicio", 1, "", "C", true);

            $pdf->Ln();
        }

        foreach ($detamov as $dt) {
            if ($elmov->tipodoc == "CPRA") {
                $pdf->Setx(2);
                $pdf->cell(20, 5, $dt->cantidad . "  " . $dt->mes . "/" . $dt->anno, 0, "", "C");
                $pdf->cell(81, 5, substr(utf8_decode($dt->producto), 0, 80), 0, "", "L");
                $pdf->cell(17, 5, number_format($dt->precosto, 4, ".", ","), 0, "", "R");
                $pdf->cell(17, 5, number_format($dt->gravado, 4, ".", ","), 0, "", "R");
                $pdf->Setx(142);
                $pdf->cell(20, 5, $dt->cantidad . "  " . $dt->mes . "/" . $dt->anno, 0, "", "C");
                $pdf->cell(81, 5, substr(utf8_decode($dt->producto), 0, 80), 0, "", "L");
                $pdf->cell(17, 5, number_format($dt->precosto, 4, ".", ","), 0, "", "R");
                $pdf->cell(17, 5, number_format($dt->gravado, 4, ".", ","), 0, "", "R");
                $pdf->Ln();
            } else {
                $pdf->Setx(2);
                $pdf->cell(15, 5, $dt->cantidad, 0, "", "C");
                $pdf->cell(25, 5, TRIM($dt->mes) . "/" . TRIM($dt->anno), 0, "", "L");
                $pdf->Setx(35);
                $pdf->cell(100, 5, $dt->producto_id . ' - ' . utf8_decode($dt->producto), 0, "", "L");
                $pdf->Setx(142);
                $pdf->cell(15, 5, $dt->cantidad, 0, "", "C");
                $pdf->cell(30, 5, TRIM($dt->mes) . "/" . TRIM($dt->anno), 0, "", "L");
                $pdf->Setx(175);
                $pdf->cell(20, 5, $dt->producto_id . ' - ' . utf8_decode($dt->producto), 0, "", "L");
                $pdf->Ln();
            }
            $tt = $tt + $dt->gravado;
        }

        if ($elmov->tipodoc == "CPRA") {
            $pdf->Setx(97);
            $pdf->cell(25, 5, "Total x Documento", 0, "", "R");
            $pdf->cell(15, 5, number_format($tt, 4, ".", ","), "T", "", "R");
            $pdf->Setx(237);
            $pdf->cell(25, 5, "Total x Documento", 0, "", "R");
            $pdf->cell(15, 5, number_format($tt, 4, ".", ","), "T", "", "R");
            $pdf->Ln();
        }
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Setx(2);
        $pdf->cell(75, 5, "ENTREGADO POR:", "T", "", "C");
        $pdf->cell(60, 5, "RECIBIDO POR:", "T", "", "C");
        $pdf->Setx(142);
        $pdf->cell(75, 5, "ENTREGADO POR:", "T", "", "C");
        $pdf->cell(60, 5, "RECIBIDO POR:", "T", "", "C");

        $pdf->Output();
        exit;
        //fin de reporte
        //envio a pdf
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
