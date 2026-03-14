<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seriesbyprov extends Model
{
    protected $table = 'seriesbyprov';

    protected $fillable = ['serie','estatus','proveedor_id','user_id'];
    
}
