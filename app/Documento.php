<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    
    protected $fillable = ['documento','nombre','correlativo',
    'movimiento','tipo','estatus','serie','docupaciente','resolucion'];
}
