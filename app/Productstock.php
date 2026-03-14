<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productstock extends Model
{
    protected $table = 'productstocks';
    
    protected $fillable = ['producto_id','existencia','bodega','clase_id'];

    public function clase()
    {
    	return $this->HasOne(Productclass::class, 'id', 'clase_id');
    }
}
