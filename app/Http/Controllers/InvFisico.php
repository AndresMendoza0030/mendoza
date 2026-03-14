<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use Storage;
use App\Product;
use App\Conteo;
use App\Fisico;
use App\User;

use App\Warehouse;


class InvFisico extends BaseController
{
    public  $xid,$xresultado,$xorden;
    protected $pdf;

    public function __construct(\App\Rpinvfisico $pdf)
    {
        $this->pdf = $pdf;
    }
    
    
    public function PDFImprimirFisico($idc)
    {
        $this->xid = $idc;
        $this->pdf->AddPage();
        $this->pdf->Output();
        //fin de reporte
        //envio a pdf
    }

    
        
    public function datosDocumento()
    {
    }
    
    public function contenido()
    {
    }
       
}
