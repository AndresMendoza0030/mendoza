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
use App\Productclass;


class PdfRpListaProducto extends BaseController
{
    public  $registros;
    protected $pdf;

    public function __construct(\App\RPListaProducto $pdf)
    {
        $this->pdf = $pdf;
    }
    
    
    public function PDFCatalogo()
    {
        //
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetTitle("Lista Producto");

        $this->contenido();
        $this->pdf->Output();
        exit;
        //
    }

   
    public function contenido()
    {
        //
        $detalle = Product::leftjoin('productclasses as cl','products.clase','=','cl.id')
        ->select('products.*','cl.nombre as claseprod')
        ->orderby('clase')->orderby('nombre')->get();
        //
        //
        $tt=0;
        $r=1;
        $cl=$detalle[0]['clase'];
        $primero=1;
        //
        $this->pdf->SetFont('Times','',8);
        //
        foreach($detalle as $det)
        {
            if ($cl != $det->clase || $primero==1)
            {
                $this->pdf->Ln();
                $this->pdf->SetFont('Times','B',8);
                $this->pdf->setX(11);
                $this->pdf->cell(40,5,utf8_decode($det->claseprod),0,"","L");
                $this->pdf->Ln();
                $this->pdf->SetFont('Times','',8);
                //
                $cl=$det->clase;                
                $primero+=1;
            }

            $this->pdf->cell(8,5,$r,0,"","C");
            $this->pdf->cell(10,5,$det->id,0,"","R");            
            $this->pdf->cell(100,4,utf8_decode($det->nombre),0,"","L");
            $this->pdf->cell(37,4,utf8_decode($det->presentacion),0,"","L");
            $this->pdf->cell(37,4,utf8_decode($det->manejopor),0,"","L");            
            $this->pdf->Ln();
            $r+=1;
        }
        //
        //
    }

    
}
