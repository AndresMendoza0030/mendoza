<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resolusbyprov  extends Model
{
    protected $table = 'resolusbyprov';
    
    protected $fillable = ['resolucion','estatus','proveedor_id','user_id'];
}
