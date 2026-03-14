<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';
    
    protected $fillable = ['nombre','barra','presentacion','tipo','estatus','clase',
                         'linea','lineafac','fabricante','registro','principio','esperfil','user_id',
                         'manejopor','costo','ultimocosto','costoanterior','registrosanitario','activo',
                         'precioventa','ultimacompra','ultimaventa','cambioprecio','descuento','preciosugerido'];

    public function clase()
    {
    	return $this->HasOne(Productclass::class, 'id', 'clase');
    }

    public function linea()
    {
    	return $this->HasOne(Productlines::class, 'id', 'linea');
    }

    public function fabricante()
    {
    	return $this->HasOne(Manufacturer::class, 'id', 'fabricante');
    }

    public function clasefac()
    {
    	return $this->HasOne(Productclassinvoice::class, 'id', 'lineafac');
    }

}
