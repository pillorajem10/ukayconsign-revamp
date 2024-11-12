<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    // Specify the table name (since it's not 'billings' by default)
    protected $table = 'usc_billings';

    // Define the primary key (optional, as 'id' is the default primary key)
    protected $primaryKey = 'id';

    // Define the fields that are mass assignable
    protected $fillable = [
        'bill_issued',
        'status',
        'user_id',
        'total_bill',
        'billing_breakdown',  // Added this field to the fillable array
        'sales_date_range',   // Add the sales_date_range field here
        'payment_platform',   // New field for payment platform
        'proof_of_payment',   // New field for proof of payment (binary data)
    ];

    // Relationship with the User model (if applicable)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
