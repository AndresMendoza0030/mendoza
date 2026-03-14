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
use App\Destino;
use App\Movdetail;
use App\Warehouse;

class PdfRpConsumoPeriodoSum extends BaseController
{
    public  $registros, $desde, $hasta;
    protected $pdf;

    public function __construct(\App\RpConsumoSum $pdf)
    {
        $this->pdf = $pdf;
    }
    
    
    public function PDFConsumoPeriodoZ($desde, $hasta,$bod)
    {
        //
        $this->pdf->desde = $desde;
        $this->pdf->hasta = $hasta;
        $this->desde = $desde;
        $this->hasta = $hasta;

        if (intval($bod)>0)
        {
            $this->pdf->labodega=Warehouse::find($bod)->nombre;
            $this->bodega=$bod;
        }
        else
        {
            $this->pdf->labodega="TODAS LA BODEGAS";
            $this->bodega=0;
        }

        //
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("Consumo-Destino-Sumarizado");

        $this->contenido();
        $this->pdf->Output();
        exit;
        //
    }
   
    public function PDFConsumoPeriodoZS($tp,$desde, $hasta,$bod)
    {
        //
        $this->pdf->desde = $desde;
        $this->pdf->hasta = $hasta;
        $this->desde = $desde;
        $this->hasta = $hasta;

        if (intval($bod)>0)
        {
            $this->pdf->labodega=Warehouse::find($bod)->nombre;
            $this->bodega=$bod;
        }
        else
        {
            $this->pdf->labodega="TODAS LA BODEGAS";
            $this->bodega=0;
        }
        //
        if($tp != '')
        {
            $midestino=auth()->user()->destino_pred;
            if ($midestino>0)
                $this->pdf->eltipo=" DESTINO: ".
                Destino::find($midestino)->nombre;
            else
                $this->pdf->eltipo='';
        }
        //
        //
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("Consumo-Destino-Sumarizado");

        $this->contenido2();
        $this->pdf->Output();
        exit;
        //
    }


    public function contenido()
    {
        //
        $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
        ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
        ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
        ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase',
        DB::raw("SUM(movdetails.gravado) as tgravado"))
        ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')->where('movdetails.bodega',$this->bodega)
        ->whereDate('movdetails.fecha','>=',$this->desde)->whereDate('movdetails.fecha','<=',$this->hasta)
        ->groupby('ih.destinogasto')->groupby('movdetails.clase')
        ->orderby('ih.destinogasto')->orderby('movdetails.clase')->get();
        //
        //
        $tt=0;
        $r=1;
        //
        $t1=0;
        $t2=0;
        $t3=0;
        //
        $tt1=0;
        $tt2=0;
        $tt3=0;
        //
        $this->pdf->SetFont('Times','',8);
        $this->pdf->cell(8,5,"No.",1,"","C");
        $this->pdf->cell(60,5,"NOMBRE DEL DESTINO",1,"","C");
        $this->pdf->cell(60,5,"CLASE PRODUCTO",1,"","C");
        $this->pdf->cell(20,5,"MONTO",1,"","C");
        $this->pdf->Ln();

        //
        $this->pdf->SetFont('Times','',8);
        //
        foreach($detalle as $det)
        {
            $this->pdf->cell(8,5,$r,0,"","C");
            $this->pdf->cell(60,5,utf8_decode($det->destino),0,"","L");
            $this->pdf->cell(60,5,utf8_decode($det->cclase),0,"","L");            
            $this->pdf->cell(20,5,number_format($det->tgravado,4),0,"","R");
            $this->pdf->Ln();
            //
            $t1+=$det->tgravado;
            //
            $tt1+=$det->tgravado;
            //
            //

            $r+=1;
        }
        //
        $this->pdf->SetFont('Times','B',8);
        //
        //
        $this->pdf->setX(10);
        $this->pdf->cell(128,5,utf8_decode("TOTAL DEL PERIODO:"),1,"","L");
        $this->pdf->cell(20,5,number_format($tt1,4),1,"","R");
        $this->pdf->Ln();
        $this->pdf->SetFont('Times','',8);
        //
    }


    public function contenido2()
    {
        //
        $midestino=auth()->user()->destino_pred;
        //
        if (intval($this->bodega) != 0)
        {
        $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
        ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
        ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
        ->leftjoin('warehouses as w','w.id','ih.bodega')        
        ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase', 'w.nombre as wnombre',
        DB::raw("SUM(movdetails.gravado) as tgravado"))
        ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')->where('ih.destinogasto',$midestino)
        ->where('movdetails.bodega',$this->bodega)
        ->whereDate('movdetails.fecha','>=',$this->desde)->whereDate('movdetails.fecha','<=',$this->hasta)
        ->groupby('ih.destinogasto')->groupby('movdetails.clase')
        ->orderby('ih.destinogasto')->orderby('movdetails.clase')->get();
        }
        else
        {
            $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
            ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
            ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
            ->leftjoin('warehouses as w','w.id','ih.bodega')
            ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase','w.nombre as wnombre',
            DB::raw("SUM(movdetails.gravado) as tgravado"))
            ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')->where('ih.destinogasto',$midestino)
            ->whereDate('movdetails.fecha','>=',$this->desde)->whereDate('movdetails.fecha','<=',$this->hasta)
            ->groupby('ih.bodega')->groupby('ih.destinogasto')->groupby('movdetails.clase')
            ->orderby('ih.bodega')->orderby('ih.destinogasto')->orderby('movdetails.clase')->get();
        }
        //
        //
        $tt=0;
        $r=1;
        //
        $t1=0;
        $t2=0;
        $t3=0;
        //
        $tt1=0;
        $tt2=0;
        $tt3=0;
        //
        $this->pdf->SetFont('Times','',8);
        $this->pdf->cell(8,5,"No.",1,"","C");
        $this->pdf->cell(60,5,"NOMBRE DEL BODEGA",1,"","C");
        $this->pdf->cell(60,5,"CLASE PRODUCTO",1,"","C");
        $this->pdf->cell(20,5,"MONTO",1,"","C");
        $this->pdf->Ln();

        //
        $this->pdf->SetFont('Times','',8);
        //
        foreach($detalle as $det)
        {
            $this->pdf->cell(8,5,$r,0,"","C");
            $this->pdf->cell(60,5,utf8_decode($det->wnombre),0,"","L");
            $this->pdf->cell(60,5,utf8_decode($det->cclase),0,"","L");            
            $this->pdf->cell(20,5,number_format($det->tgravado,4),0,"","R");
            $this->pdf->Ln();
            //
            $t1+=$det->tgravado;
            //
            $tt1+=$det->tgravado;
            //
            //

            $r+=1;
        }
        //
        $this->pdf->SetFont('Times','B',8);
        //
        //
        $this->pdf->setX(10);
        $this->pdf->cell(128,5,utf8_decode("TOTAL DEL PERIODO:"),1,"","L");
        $this->pdf->cell(20,5,number_format($tt1,4),1,"","R");
        $this->pdf->Ln();
        $this->pdf->SetFont('Times','',8);
        //
    }
    

}
