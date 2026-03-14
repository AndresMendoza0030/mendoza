<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;
use Carbon\Carbon;

class RpConsumoDet extends Fpdf
{
    public $desde,$hasta, $labodega, $eltipo;

    public function Header()
    {
        $this->SetFont('Arial','B',8);
        $this->SetY(10);
        $this->SetX(0);
        $this->Cell(0,5,utf8_decode('MENDOZA ALVARADO, SA DE CV'),0,0,'C');
        $this->Cell(5,5,'Pg.: '.$this->PageNo().'/{nb}',0,0,'R');
        $this->Ln();
        $this->SetX(0);
        $this->Cell(0,5,utf8_decode("DETALLE DE CONSUMOS POR AREA, DESDE: ").date('d-m-Y', strtotime($this->desde))." HASTA ".date('d-m-Y', strtotime($this->hasta)),0,0,'C');
        $this->Ln();
        $this->Cell(0,5,"BODEGA: ".utf8_decode(TRIM($this->labodega))."  ".utf8_decode(TRIM($this->eltipo)),0,0,'C');
        $this->Ln();
        $this->Ln();
        //
    }

    public function Footer()
    {
    }
}