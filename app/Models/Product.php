<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'SKU';
    public $incrementing = false;

    // Disable timestamps
    public $timestamps = false;

    protected $fillable = [
        'SKU', 'Bundle', 'ProductID', 'Type', 'Style', 'Color', 
        'Gender', 'Category', 'Bundle_Qty', 'Consign', 'Cash', 
        'SRP', 'maxSRP', 'PotentialProfit', 'Date', 'Cost', 
        'Stock', 'Supplier', 'Image', 'Secondary_Img', 
        'Img_color', 'is_hidden', 'Batch_number', 'Bale', 
        'createdAt'
    ];

    protected $dates = [
        'Date', 'createdAt'
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class, 'SKU', 'SKU');
    }
    
    public function productBarcodes()
    {
        return $this->hasMany(ProductBarcode::class, 'product_sku', 'SKU');
    }

    public function receivedProducts()
    {
        return $this->hasMany(ReceivedProduct::class, 'product_sku', 'SKU');
    }
}
