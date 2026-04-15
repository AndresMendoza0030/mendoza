<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseImportItem extends Model
{
    protected $fillable = [
        'purchase_import_id',
        'line_no',
        'extracted_code',
        'extracted_description',
        'extracted_unit',
        'extracted_qty',
        'extracted_unit_price',
        'extracted_subtotal',
        'matched_product_id',
        'match_score',
        'needs_review',
    ];

    protected $casts = [
        'extracted_qty' => 'decimal:4',
        'extracted_unit_price' => 'decimal:4',
        'extracted_subtotal' => 'decimal:4',
        'needs_review' => 'boolean',
    ];

    public function purchaseImport()
    {
        return $this->belongsTo(PurchaseImport::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'matched_product_id');
    }
}