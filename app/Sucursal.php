<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales_fiscales';
    
    protected $fillable = ['nombre'];

}
