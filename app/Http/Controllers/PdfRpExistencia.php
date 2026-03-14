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
use App\Warehouse;
use App\Product;
use App\Productstock;
use App\Movdetail;

class PdfRpExistencia extends BaseController
{
    public  $registros,$labodega,$fecha;
    protected $pdf;

    public function __construct(\App\RPExistencias $pdf)
    {
        $this->pdf = $pdf;
    }
    
    
    public function PDFExistencia($bodega,$xfecha)
    {
  
        //
        $xbodega=Warehouse::find($bodega);
        $cbodega=$xbodega->nombre;

        $this->labodega=$bodega;
        $this->fecha = $xfecha;

        $this->pdf->wbodega=$bodega;
        $this->pdf->cbodega = $cbodega;
        $this->pdf->lfecha=$xfecha;
        
        //

        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("General Existencias".$cbodega);

        $this->contenido();
        $this->pdf->Output();
        exit;
    }

   
    public function contenido()
    {
        //
        $detalle = Productstock::leftjoin('products as pr','productstocks.producto_id','=','pr.id')
        ->where('productstocks.bodega','=',$this->labodega)->where('productstocks.existencia','<>',0)
        ->select('productstocks.*','pr.clase as claseprod', 'pr.costo as costoprod',
        DB::raw("CONCAT(TRIM(pr.nombre)) as producto"),
        DB::raw("pr.costo*productstocks.existencia as montoexiste"))
        ->orderby('pr.clase')->orderby('pr.nombre')->orderby('pr.presentacion')
        ->get();
        //

        if ($this->fecha != date('Y-m-d'))
        {
                //
                $resexiste = collect([]);
                $r=0;
                //
                foreach ($detalle as $d)
                {
                    $cp=0;
                    //tomando ultima existenca
                    $dp=Movdetail::where('estatus',0)->where('bodega',$this->labodega)->where('producto_id',$d->producto_id)
                    ->where('fecha','<=',$this->fecha)
                    ->select('bodega','producto_id','producto','cantidad','posterior','precosto as costodet', 'tipodoc','fecha')
                    ->orderby('fecha','desc')->orderby('id','desc')->first();
    
                    //
                    if ($dp)
                    {
                        if ($dp->tipodoc=="CPRA")
                        {
                            $dpe=Movdetail::where('estatus',0)->where('bodega',$this->labodega)->where('producto_id',$d->producto_id)
                            ->where('fecha','<=',$this->fecha)->where('tipodoc','!=','CPRA')
                            ->select('bodega','producto_id','producto','cantidad','posterior','precosto as costodet', 'tipodoc','fecha')
                            ->orderby('fecha','desc')->orderby('id','desc')->first();
                            //
                            if ($dpe)
                            {
                                $cp=$dpe->costodet;
    
                            }
                            if ($cp ==0 || $cp ==null )
                            {
                                $cp=$d->costoprod;
                            }

                        }
                        else
                        {
                            $cp=$dp->costodet;
                            //
                            if ($cp ==0 || $cp ==null )
                            {
                                $cp=$d->costoprod;
                            }
                        }
                        //
                        $ct=$cp;
                        $mt=round($ct*$dp->posterior,4);
                        //
                        
                        $resexiste->push(["rg"=> $r, "producto_id"=>$d->producto_id, "producto"=>$d->producto,
                        "idclase"=>$d->clase, "idlinea"=>$d->linea, "cclase"=>$d->cclase, "clinea"=>$d->clinea,
                        "costoprod"=>$d->costo,"existencia"=>$dp->posterior, "costodet"=>$dp->costodet,
                        "costo"=>$ct, "montoexiste"=> $mt]);
                        $r+=1;
                    }
                }
                $detalle= json_decode($resexiste);
    
        }
        //////////////////////////////////////
        //
        $tt=0;
        $this->pdf->SetFont('Times','',8);

        foreach($detalle as $det)
        {
            $this->pdf->cell(135,4,$det->producto_id." - ".utf8_decode($det->producto),0,"","L");
            $this->pdf->cell(30,4,$det->existencia,0,"","R");
            $this->pdf->cell(25,4,number_format(round($det->montoexiste,2),2),0,"","R");
            $this->pdf->Ln();
            $tt+=$det->montoexiste;
            
        }
        $this->pdf->Ln();
        $this->pdf->SetFont('Times','B',8);
        $this->pdf->cell(165,4,"TOTALES:",1,"","R");
        $this->pdf->cell(25,4,number_format($tt,2),1,"","R");
        $this->pdf->Ln();
        //
    }

    
}
