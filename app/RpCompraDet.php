<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;
use Carbon\Carbon;

class RpCompraDet extends Fpdf
{

    public $desde,$hasta;
    
    public function Header()
    {
        $this->SetFont('Arial','B',8);
        $this->SetY(10);
        $this->SetX(0);    
        $this->Cell(275,5,utf8_decode('MENDOZA ALVARADO, SA DE CV'),0,0,'C');
        $this->Cell(15,5,'Pg.: '.$this->PageNo().'/{nb}',0,0,'R');
        $this->Ln();
        $this->SetX(0);    
        $this->Cell(275,5,utf8_decode("DETALLE DE COMPRA POR PERIODO ").date('d-m-Y', strtotime($this->desde))." HASTA ".date('d-m-Y', strtotime($this->hasta)),0,0,'C');
        $this->Ln();
        $this->Cell(180,5,utf8_decode(TRIM($this->labodega)),0,0,'C');
        $this->Ln();
        //
        $this->SetFont('Arial','',8);
        $this->cell(15,5,"FECHA",1,"","C");        
        $this->cell(50,5,"NOMBRE DEL PROVEEDOR.",1,"","C");
        $this->cell(20,5,"REFERENCIA",1,"","C");
        $this->cell(15,5,"GRAVADO",1,"","C");
        $this->cell(15,5,"IVA",1,"","C");        
        $this->cell(15,5,"TOTAL",1,"","C");
        $this->cell(60,5,"PRODUCTO",1,"","C"); 
        $this->cell(17,5,"CANTIDAD",1,"","C");
        $this->cell(17,5,"COSTO.UN",1,"","C");
        $this->cell(17,5,"GRAVADO",1,"","C");
        $this->cell(17,5,"IVA",1,"","C");        
        $this->cell(17,5,"TOTAL",1,"","C");
        //
        $this->Ln();
        //
    }

    public function Footer()
    {
    }
}