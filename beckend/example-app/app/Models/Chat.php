<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'interlocutor_id',
        'project_id',
        'token',
    ];

    // ===== Связи =====

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function interlocutor()
    {
        return $this->belongsTo(User::class, 'interlocutor_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Удаляет историю сообщений проекта (при смене исполнителя).
     */
    public static function purgeMessagesForProject(int $projectId): void
    {
        $chat = static::where('project_id', $projectId)->first();
        if (!$chat) {
            return;
        }

        Message::where('chat_id', $chat->id)->delete();
        $chat->forceFill(['token' => (string) Str::uuid()])->save();
    }
}