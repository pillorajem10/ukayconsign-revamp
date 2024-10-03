<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    protected $table = 'store_inventory';

    protected $primaryKey = 'id'; // Set the primary key to 'id'

    public $incrementing = true; // Primary key is an auto-incrementing integer

    protected $keyType = 'int'; // The primary key is an integer

    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'SKU',
        'ProductID',
        'Stocks',
        'Consign',
        'SPR',
        'store_id',
    ];
}
