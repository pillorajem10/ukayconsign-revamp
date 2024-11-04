<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UscReturn extends Model
{
    use HasFactory;

    protected $table = 'usc_returns';

    protected $fillable = [
        'user_id',
        'product_sku',
        'store_id',
        'quantity',
        'return_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'SKU');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
