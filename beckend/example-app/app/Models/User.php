<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory,HasApiTokens, Notifiable;

    protected $fillable = [
        'full_name',
        'phone',
        'login',
        'avatar',
        'password',
        'email_verification_code',
        'email_verified_at',
        'role',
        'balance',
        'rating',
        'is_banned',
        'isBanned',
    ];

    public function getAuthIdentifierName()
    {
        return 'login'; // Используем поле login вместо email
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_banned' => 'boolean',
        'isBanned' => 'boolean',
    ];

    public function isBanned(): bool
    {
        if (array_key_exists('isBanned', $this->attributes) && $this->attributes['isBanned'] !== null) {
            return (int) $this->attributes['isBanned'] === 1;
        }
        if (array_key_exists('is_banned', $this->attributes) && $this->attributes['is_banned'] !== null) {
            return (int) $this->attributes['is_banned'] === 1;
        }

        return false;
    }

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
