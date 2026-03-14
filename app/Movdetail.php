<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movdetail extends Model
{
    protected $table = 'movdetails';
    
    protected $fillable = ['tipodoc','numerodoc','fecha','paciente_id','bodega','clase','service_unit',
    'linea','lineafac','producto_id','producto','anterior','cantidad','posterior',
    'precosto','preventa','subtotal','pdescuento','descuento','gravado','iva','total','lotevence','sugerido','escopago',
    'tasaiva','revisado','estatus','movimiento','devuelto','solicitado','admongp','admonval','user_id','reversado'];

    public function products()
    {
    	return $this->HasOne(Products::class, 'producto_id');
    }
}
