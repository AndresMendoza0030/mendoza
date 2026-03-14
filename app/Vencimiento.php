<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vencimiento extends Model
{
    protected $table = 'vencimientos';

    protected $fillable = ['id','bodega','producto_id','mes','anno','entradas','salidas','total'];

}
