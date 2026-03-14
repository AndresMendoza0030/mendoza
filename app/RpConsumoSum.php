<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;
use Carbon\Carbon;

class RpConsumoSum extends Fpdf
{
    public $desde, $hasta, $labodega;    
    
    public function Header()
    {
        $this->SetFont('Times','B',8);
        $this->SetY(10);
        $this->SetX(0);    
        $this->Cell(0,5,utf8_decode('MENDOZA ALVARADO, SA DE CV'),0,0,'C');
        $this->Cell(5,5,'Pg.: '.$this->PageNo().'/{nb}',0,0,'R');
        $this->Ln();
        $this->SetX(0);    
        $this->Cell(0,5,utf8_decode("RESUMEN DE CONSUMOS X DESTINO POR PERIODO ").date('d-m-Y', strtotime($this->desde))." HASTA ".date('d-m-Y', strtotime($this->hasta)),0,0,'C');
        $this->Ln();
        $this->Cell(0,5,utf8_decode(TRIM($this->labodega)),0,0,'C');
        $this->Ln();
    }

    public function Footer()
    {
    }
}