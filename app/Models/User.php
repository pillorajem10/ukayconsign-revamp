<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'email',
        'password',
        'role',
        'verified',
        'verification_token',
        'fname',
        'lname',
    ];

    protected $hidden = [
        'password',
        'verification_token',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define relationship to Cart model
    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id'); // Specify the foreign key
    }
}
