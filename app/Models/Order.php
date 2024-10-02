<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'orders';

    // Specify the primary key (if different from the default 'id')
    protected $primaryKey = 'id';

    // Disable timestamps if not using them
    public $timestamps = false; // Set to true if using created_at and updated_at columns

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'products_ordered',
        'address',
        'store_name',
        'email',
        'total_price',
        'order_date',
        'order_status',
        'createdAt',
    ];

    // Optionally, define casts for attributes
    protected $casts = [
        'total_price' => 'decimal:2',
        'order_date' => 'datetime',
        'createdAt' => 'datetime',
    ];
}
