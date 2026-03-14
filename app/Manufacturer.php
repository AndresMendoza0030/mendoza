<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = 'manufacturers';
    
    protected $fillable = ['nombre','estatus'];

    public function products()
    {
    	return $this->belongsTo(Products::class, 'fabricante');
    }
}
