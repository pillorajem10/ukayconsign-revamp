<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstantBuyProduct extends Model
{
    use HasFactory;

    protected $table = 'instant_buy_products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'product_barcode',
        'name',
        'description',
        'size',
        'dimension',
        'price',
        'images',
        'issue',
        'model',
        'store_id',
        'video',
    ];

    protected $casts = [
        'images' => 'array', // Keep images as an array
    ];
}

