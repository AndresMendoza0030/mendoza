<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{

    protected $table = 'warehouses';

    protected $fillable = ['nombre','cuenta','estatus','surtidora'];

        
}
