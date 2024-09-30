<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers'; // Specify the table name
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = true; // Primary key is auto-incrementing
    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'supplier_name', // Fillable fields
        'createdAt',
        'updatedAt',
    ];
}
