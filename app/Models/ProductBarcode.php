<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model
{
    use HasFactory;

    protected $table = 'usc_product_barcodes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'product_sku',
        'barcode_image',
        'barcode_number',
        'is_used',
        'received_product_id',
        'batch_number',
    ];

    // No hidden attributes
    // protected $hidden = []; // Optional: leave this out if not hiding anything

    protected $casts = [
        'is_used' => 'boolean',
    ];
}
