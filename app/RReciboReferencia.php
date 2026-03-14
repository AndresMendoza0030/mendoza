<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;


class RReciboReferencia extends Fpdf
{

    public  $elmes, $elanno ;


    public function Header()
    {

    }

    
    public function Footer()
    {
    }
}