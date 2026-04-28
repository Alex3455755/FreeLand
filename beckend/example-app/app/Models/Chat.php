<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}