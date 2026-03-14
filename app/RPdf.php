<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Crabbly\Fpdf\Fpdf;


class RPdf extends Fpdf
{
    public function Header()
    {
        // Logo
        $filepath = public_path() . '\images\logo\rxhead.jpg';
        $this->Image($filepath,2,2);
        // Salto de línea
        $this->Sety(40);
    }

    public function Footer()
    {
        $this->SetY(-100);
        $filepath = public_path() . '\images\logo\rxfoot.jpg';
        $this->Image($filepath,-3,273);
    }
}