<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Change this line
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // Extend from Authenticatable
{
    use HasFactory, Notifiable; // Include Notifiable trait for notifications

    // Specify the table associated with the model
    protected $table = 'users';

    // Specify the primary key (if different from the default 'id')
    protected $primaryKey = 'id';

    // Enable timestamps if you are using them
    public $timestamps = true;

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'email',
        'password',
        'role',
        'verified',
        'verification_token',
    ];

    // Define hidden attributes (e.g., password)
    protected $hidden = [
        'password',
        'verification_token',
    ];

    // Optionally, you can define casts for attributes
    protected $casts = [
        'verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
