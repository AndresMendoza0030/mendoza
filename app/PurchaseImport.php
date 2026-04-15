<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseImport extends Model
{
    protected $fillable = [
        'supplier_name',
        'supplier_nit',
        'supplier_nrc',
        'document_number',
        'control_number',
        'generation_code',
        'issue_date',
        'subtotal',
        'iva',
        'total',
        'payment_terms',
        'raw_text',
        'image_path',
        'status',
        'created_by',
        'confirmed_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseImportItem::class);
    }
}