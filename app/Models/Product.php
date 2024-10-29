<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'usc_products';

    // Set the primary key field
    protected $primaryKey = 'SKU';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Disable automatic timestamps
    public $timestamps = false;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'SKU', 'Bundle', 'ProductID', 'Type', 'Style', 'Color', 
        'Gender', 'Category', 'Bundle_Qty', 'Consign', 
        'SRP', 'PotentialProfit', 'Date', 'Cost', 
        'Stock', 'Supplier', 'Image', 'details_images', 
        'Secondary_Img', 'Img_color', 'is_hidden', 
        'Batch_number', 'Bale', 'createdAt'
    ];

    // Define the date attributes for date casting
    protected $dates = [
        'Date', 'createdAt'
    ];

    // Optionally, you can cast 'details_images' to an array
    protected $casts = [
        'details_images' => 'array', // Automatically handles JSON encoding/decoding
    ];

    // Define relationships

    /**
     * Get the batches associated with the product.
     */
    public function batches()
    {
        return $this->hasMany(Batch::class, 'SKU', 'SKU');
    }

    /**
     * Get the barcodes associated with the product.
     */
    public function productBarcodes()
    {
        return $this->hasMany(ProductBarcode::class, 'product_sku', 'SKU');
    }

    /**
     * Get the received products associated with the product.
     */
    public function receivedProducts()
    {
        return $this->hasMany(ReceivedProduct::class, 'product_sku', 'SKU');
    }
}
