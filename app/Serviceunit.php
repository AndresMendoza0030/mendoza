<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serviceunit extends Model
{
    protected $table = 'service_units';
    
    protected $fillable = ['nombre','cuentacontable','serviceclass','invoiceclass','estatus'];

}
