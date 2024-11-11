<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosCart extends Model
{
    use HasFactory;

    protected $table = 'usc_pos_cart'; // Set your table name here

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
        'store_id', // New field added
        'barcode_numbers', // Add this field to the fillable array
    ];

    protected $casts = [
        'date_added' => 'datetime',
        'price' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'orig_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'barcode_numbers' => 'array', // Cast it to array if you're saving it as a JSON-like structure
    ];

    public $timestamps = false; // Set to true if you have created_at and updated_at columns
}
