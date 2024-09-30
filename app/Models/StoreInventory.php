<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    protected $table = 'store_inventory';

    protected $fillable = [
        'SKU',
        'ProductID',
        'Stocks',
        'Consign',
        'SPR',
        'store_id',
    ];
}
