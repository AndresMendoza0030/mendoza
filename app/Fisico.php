<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fisico extends Model
{
    protected $table = 'inv_fisico';
    
    protected $fillable = ['conteo_id','producto_id','actual','contado','diferencia','tipoajuste','bodega_id','user_id'];
}
