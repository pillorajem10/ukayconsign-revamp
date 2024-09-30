<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'store_name',
        'store_owner',
        'store_address',
        'store_phone_number',
        'store_email',
        'store_total_earnings',
        'store_status', 
    ];    
}
