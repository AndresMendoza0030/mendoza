<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;
use Carbon\Carbon;

class RPListaProducto extends Fpdf
{

    
    public function Header()
    {
        $this->SetFont('Times','B',8);
        $this->SetY(10);
        $this->SetX(0);    
        $this->Cell(180,5,utf8_decode('MENDOZA ALVARADO, SA DE CV'),0,0,'C');
        $this->Cell(15,5,'Pg.: '.$this->PageNo().'/{nb}',0,0,'R');
        $this->Ln();
        $this->SetX(0);    
        $this->Cell(180,5,utf8_decode("CATALOGO GENERAL DE PRODUCTOS ORDENADO POR PCLASE"),0,0,'C');
        $this->Ln();
        $this->Ln();

        $this->SetFont('Times','',8);
        $this->cell(8,5,"No.",1,"","C");
        $this->cell(10,5,"ID.",1,"","C");        
        $this->cell(100,5,"NOMBRE DEL PRODUCTO.",1,"","C");
        $this->cell(37,5,"PRESENTACION",1,"","C");
        $this->cell(37,5,"MANEJO",1,"","C");
        $this->Ln();
    }

    public function Footer()
    {
    }
}