<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;


class Rpinvfisico extends Fpdf
{
    public function Header()
    {
        $this->SetFont("Courier","B",11);
        $this->Cell(0,6,"MENDOZA ALVARADO, SA DE CV",0,"","C");
        $this->Ln();
        $this->Cell(0,6,utf8_decode("Conteo Físico de Inventario en Bodega"),0,"","C");
        $this->Ln(3);
    }

    public function Footer()
    {
     $this->SetY(-15);
     $this->SetFont('Courier','I',8);
     $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');     
    }
}