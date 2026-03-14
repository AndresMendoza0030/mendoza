<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VenceFisico extends Model
{
    protected $table = 'vence_fisico';
    
    protected $fillable = ['idvence','idconteo','producto_id','mes','anno','habia','conteo',
    'procesado','user_id','diferencia','tipomovi'];
}
