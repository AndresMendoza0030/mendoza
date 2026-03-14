<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sysparam extends Model
{
    protected $table = 'sysparam';

    protected $fillable = ['cumplebodega','cumpledestino','vencimiento','cuentaprovee',
'cuentabodega','cuentacredfiscal', 'cuentapercibecompra','cuentaretieneventa',
'cuentarentaretenica','mesactivoconta','annoactivoconta','mesactivoiva','annoactivoiva',
'descuentoempleadoservicio','descuentosocioservicio','fiscal','bodegaclinica','bodegacumple'];
    
}
