<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tally extends Model
{
    use HasFactory;

    protected $table = 'usc_tallies'; // Specify the correct table name
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Disable Eloquent timestamps

    protected $fillable = [
        'total',
        'store_id',
        'day',
        'month',
        'year',
        'createdAt',
    ];

    protected $dates = [
        'createdAt', // Include createdAt as a date field
    ];

    // In Tally.php model
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
