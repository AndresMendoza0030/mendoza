<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAlias extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_name',
        'alias_text',
        'normalized_alias',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}