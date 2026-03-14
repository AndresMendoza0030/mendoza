<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    
    protected $fillable = ['ciudad','depto_id','departamento'];
}
