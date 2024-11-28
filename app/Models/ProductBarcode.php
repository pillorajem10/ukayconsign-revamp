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
        'received_product_id',  // The foreign key for the relationship
        'batch_number',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    public function receivedProduct()
    {
        return $this->belongsTo(ReceivedProduct::class, 'received_product_id', 'id');
    }
}

