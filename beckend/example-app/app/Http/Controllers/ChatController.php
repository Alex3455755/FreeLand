<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Список чатов пользователя
     */
    public function index()
    {
        $userId = Auth::id();

        $chats = Chat::with(['author', 'interlocutor', 'project'])
            ->where('author_id', $userId)
            ->orWhere('interlocutor_id', $userId)
            ->latest()
            ->get();

        return response()->json($chats);
    }

    /**
     * Создание чата
     */
    public function store(Request $request)
    {
        $request->validate([
            'interlocutor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        $userId = Auth::id();

        // защита от дубля чата
        $chat = Chat::where('project_id', $request->project_id)
            ->where(function ($q) use ($userId, $request) {
                $q->where(function ($q2) use ($userId, $request) {
                    $q2->where('author_id', $userId)
                       ->where('interlocutor_id', $request->interlocutor_id);
                })->orWhere(function ($q2) use ($userId, $request) {
                    $q2->where('author_id', $request->interlocutor_id)
                       ->where('interlocutor_id', $userId);
                });
            })
            ->first();

        if ($chat) {
            return response()->json($chat);
        }

        $chat = Chat::create([
            'author_id' => $userId,
            'interlocutor_id' => $request->interlocutor_id,
            'project_id' => $request->project_id,
            'token' => Str::uuid(),
        ]);

        return response()->json($chat, 201);
    }

    /**
     * Показ чата + сообщения
     */
    public function show(Chat $chat)
    {
        $chat->load([
            'author',
            'interlocutor',
            'project',
            'messages.author'
        ]);

        return response()->json($chat);
    }

    /**
     * Отправка сообщения
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'author_id' => Auth::id(),
            'text' => $request->text,
            'time' => now(),
        ]);

        return response()->json($message, 201);
    }

    /**
     * Удаление чата
     */
    public function destroy(Chat $chat)
    {
        $chat->delete();

        return response()->json(['message' => 'Chat deleted']);
    }
}