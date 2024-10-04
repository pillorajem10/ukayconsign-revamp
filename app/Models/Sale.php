<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'usc_sales'; // Specify the correct table name
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Disable default timestamps if you are managing them manually

    protected $fillable = [
        'customer_name',
        'customer_number',
        'total',
        'mode_of_payment',
        'date_of_transaction',
        'ordered_items',
        'sale_made',
        'ref_number_ewallet',
        'amount_paid',
        'cx_change',
        'processed_by',
        'createdAt',
        'cx_type',
    ];

    protected $casts = [
        'date_of_transaction' => 'datetime',
        'createdAt' => 'datetime',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'cx_change' => 'decimal:2',
    ];
}
