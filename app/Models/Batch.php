<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';
    protected $primaryKey = 'Batch_number';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'SKU', 
        'Bundle', 
        'ProductID', 
        'Type', 
        'Style', 
        'Color', 
        'Gender', 
        'Category', 
        'Bundle_Qty', 
        'Consign', 
        'SRP', 
        'PotentialProfit', 
        'Cost', 
        'Stock', 
        'Supplier', 
        'Img_color', 
        'Bale', 
        'Batch_number', 
        'createdAt'
    ];

    protected $dates = [
        'createdAt'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'SKU', 'SKU');
    }
}
