<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;
use Carbon\Carbon;

class RPExistencias extends Fpdf
{

    public  $wbodega, $cbodega, $lfecha;


    public function Header()
    {
        $this->SetFont('Times','B',8);
        $this->SetY(10);
        $this->SetX(0);    
        $this->Cell(180,5,utf8_decode('MENDOZA ALVARADO, SA DE CV'),0,0,'C');
        $this->Cell(15,5,'Pg.: '.$this->PageNo().'/{nb}',0,0,'R');
        $this->Ln();
        $this->SetX(0);    
        $this->Cell(180,5,utf8_decode("EXISTENCIA DE PRODUCTO X BODEGA"),0,0,'C');
        $this->Ln();
        $this->SetX(0);        
        $this->Cell(180,5,utf8_decode("BODEGA: ".$this->cbodega)."   HASTA LA FECHA: ".date('d-m-Y', strtotime($this->lfecha)),0,0,'C');
        $this->Ln();

        $this->SetFont('Times','',8);
        $this->cell(135,5,"NOMBRE DEL PRODUCTO",1,"","C");
        $this->cell(30,5,"EXISTENCIA",1,"","C");
        $this->cell(25,5,"MTO.EXISTENCIA",1,"","C");
        $this->Ln();
    }

    public function Footer()
    {
    }
}