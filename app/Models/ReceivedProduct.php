<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedProduct extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'received_products';

    // Specify the primary key (if different from the default 'id')
    protected $primaryKey = 'id';

    // Disable timestamps if not using them
    public $timestamps = false; // Set to true if using createdAt and updatedAt fields

    // Define fillable attributes for mass assignment
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

    // Optionally, you can define casts for attributes
    protected $casts = [
        'printed_barcodes' => 'boolean',
        'is_voided' => 'boolean',
        'createdAt' => 'datetime',
    ];
}
