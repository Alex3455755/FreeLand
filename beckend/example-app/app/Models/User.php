<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'phone',
        'login',
        'password',
        'role',
        'balance',
        'rating',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function customerProjects()
    {
        return $this->hasMany(Project::class, 'customer_id');
    }

    public function freelancerPayments()
    {
        return $this->hasMany(Payment::class, 'freelancer_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
