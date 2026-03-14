<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detareq extends Model
{
    protected $table = 'detareqs';
    
    protected $fillable = ['tipodoc','numerodoc','fecha', 'bodega','producto_id','producto','cantidad','autorizado','precosto',
    'preventa','subtotal','user_id','estatus','idoriginal','lotevence','comentario'];
}
