<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // User ID linking to the User model
        'product_sku',    // SKU linking to the Product model
        'quantity',       // Quantity of the product
        'price_type',     // Type of price (e.g., regular, discount)
        'price',          // Price of the product
        'added_at',       // Timestamp when the product was added to the cart
    ];

    // Disable timestamps
    public $timestamps = false;

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the Product model using SKU
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'sku'); // Assuming 'sku' is the primary key in Product
    }
}