<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    protected $table = 'destinos';
    
    protected $fillable = ['nombre','cuenta','estatus'];

}
