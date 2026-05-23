<?php

namespace App\Models;

use App\Casts\EncryptedText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'author_id',
        'text',
        'time',
        'read_at',
        'attachment_path',
        'attachment_name',
    ];

    protected $casts = [
        'time' => 'datetime',
        'read_at' => 'datetime',
        'text' => EncryptedText::class,
    ];

    protected $hidden = [
        'attachment_path',
    ];

    protected $appends = [
        'attachment_url',
    ];

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return url(Storage::disk('public')->url($this->attachment_path));
    }

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