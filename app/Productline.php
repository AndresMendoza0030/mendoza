<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productline extends Model
{
    protected $table = 'productlines';
    
    protected $fillable = ['nombre','cuenta','estatus','clase_id'];

    public function clase()
    {
    	return $this->HasOne(Productclass::class, 'id', 'clase_id');
    }

}
