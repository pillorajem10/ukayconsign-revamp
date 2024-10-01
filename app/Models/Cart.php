<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'carts';

    // Specify the primary key (if different from the default 'id')
    protected $primaryKey = 'id';

    // Disable timestamps if not using them
    public $timestamps = false; // Set to true if using created_at and updated_at columns

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'product_sku',
        'quantity',
        'price_type',
        'price',
        'added_at', // You can keep this if you want to track when items were added
    ];

    // Optionally, define casts for attributes
    protected $casts = [
        'price' => 'decimal:2',
        'added_at' => 'datetime', // Use this if you want to handle date formats
    ];

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the Product model using SKU
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'SKU'); // Assuming 'sku' is the primary key in the Product model
    }
}
