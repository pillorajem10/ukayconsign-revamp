<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxInfo extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional, if not following Laravel's naming convention)
    protected $table = 'cx_infos';

    // Define the primary key (optional, since it's default to 'id')
    protected $primaryKey = 'id';

    // Define which columns can be mass-assigned
    protected $fillable = [
        'cx_name',
        'email',
        'phone_number',
        'cx_type',
        'interest',
        'remarks',
        'store_id',
    ];

    // Define which attributes should be cast to specific data types
    protected $casts = [
        'store_id' => 'integer',
    ];

    // If you're using timestamps, you can set this to true; otherwise, leave as false
    public $timestamps = false;  // Set to true if your table has `created_at` and `updated_at` columns
}
