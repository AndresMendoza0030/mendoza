<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productclass extends Model
{

    protected $table = 'productclasses';

    protected $fillable = ['nombre','cuenta','estatus'];

        
}
