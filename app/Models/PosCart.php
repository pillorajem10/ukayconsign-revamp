<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosCart extends Model
{
    use HasFactory;

    protected $table = 'usc_pos_cart';

    protected $fillable = [
        'product_sku',
        'quantity',
        'price',
        'date_added',
        'sub_total',
        'product_bundle_id',
        'orig_total',
        'discount',
        'user',
        'store_id',
        'barcode_numbers',
        'consign_price',
        'consign_total',
    ];

    protected $casts = [
        'date_added' => 'datetime',
        'price' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'orig_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'barcode_numbers' => 'array',
        'consign_price' => 'decimal:2',
        'consign_total' => 'decimal:2',
    ];

    public $timestamps = false;
}
