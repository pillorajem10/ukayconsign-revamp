<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedProduct extends Model
{
    use HasFactory;

    protected $table = 'usc_received_products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'supplier',
        'product_sku',
        'quantity_received',
        'printed_barcodes',
        'is_voided',
        'bale',
        'batch_number',
        'cost',
    ];

    protected $casts = [
        'printed_barcodes' => 'boolean',
        'is_voided' => 'boolean',
        'createdAt' => 'datetime',
    ];
}

