<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Claserole extends Model
{
    protected $table = 'clases_roles';
    
    protected $fillable = ['clase','role'];

}
