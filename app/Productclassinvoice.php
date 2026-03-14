<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productclassinvoice extends Model
{
    protected $table = 'productclassinvoices';
    
    protected $fillable = ['nombre','cuenta','admon','estatus','comision','admongp'];


}

