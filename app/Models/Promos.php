<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promos extends Model
{
    protected $table = 'promos';

    protected $fillable = ['image'];

    public $timestamps = false; // Since 'created_at' is managed by MySQL
}
