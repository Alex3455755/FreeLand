<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'author_id',
        'text',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    // ===== Связи =====

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}