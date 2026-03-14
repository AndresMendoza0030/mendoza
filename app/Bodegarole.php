<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bodegarole extends Model
{
    protected $table = 'bodegas_roles';
    
    protected $fillable = ['bodega','role'];

}
