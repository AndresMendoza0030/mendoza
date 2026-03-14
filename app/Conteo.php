<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conteo extends Model
{
    protected $table = 'conteo_head';
    
    protected $fillable = ['fecha','bodega_id','concepto','user_id','procesado','estatus'];
}
