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
    public function index(Request $request)
    {
        $userId = $this->resolveUserId($request);

        $chats = Chat::with(['author', 'interlocutor', 'project'])
            ->where(function ($query) use ($userId) {
                $query->where('author_id', $userId)
                    ->orWhere('interlocutor_id', $userId);
            })
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ]);
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
    public function show(Request $request, Chat $chat)
    {
        $this->authorizeParticipant($request, $chat);
        $userId = $this->resolveUserId($request);

        Message::where('chat_id', $chat->id)
            ->where('author_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $chat->load([
            'author',
            'interlocutor',
            'project',
            'messages.author'
        ]);

        return response()->json($chat);
    }

    /**
     * Количество непрочитанных сообщений
     */
    public function unreadCount(Request $request)
    {
        $userId = $this->resolveUserId($request);

        $count = Message::whereHas('chat', function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('author_id', $userId)
                        ->orWhere('interlocutor_id', $userId);
                });
            })
            ->where('author_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Отправка сообщения (текст и/или файл)
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $this->authorizeParticipant($request, $chat);

        $request->validate([
            'text' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,gif,webp,pdf,zip,doc,docx,xls,xlsx,txt,csv',
        ]);

        $hasFile = $request->hasFile('file');
        $text = trim((string) $request->input('text', ''));

        if (!$hasFile && $text === '') {
            return response()->json([
                'success' => false,
                'message' => 'Введите текст или прикрепите файл',
            ], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($hasFile) {
            $uploaded = $request->file('file');
            $attachmentName = $uploaded->getClientOriginalName();
            $attachmentPath = $uploaded->store('chat_attachments', 'public');
        }

        $displayText = $text;
        if ($displayText === '' && $attachmentName) {
            $displayText = '📎 '.$attachmentName;
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'author_id' => $this->resolveUserId($request),
            'text' => $displayText,
            'time' => now(),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $message->load('author');

        $chat->update([
            'token' => Str::uuid(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'chat_token' => $chat->token,
        ], 201);
    }

    /**
     * Удаление чата
     */
    public function destroy(Request $request, Chat $chat)
    {
        $this->authorizeParticipant($request, $chat);

        $chat->delete();

        return response()->json(['message' => 'Chat deleted']);
    }

    private function authorizeParticipant(Request $request, Chat $chat): void
    {
        $userId = $this->resolveUserId($request);
        if ((int) $chat->author_id !== (int) $userId && (int) $chat->interlocutor_id !== (int) $userId) {
            abort(403, 'Forbidden');
        }
    }

    private function resolveUserId(Request $request): ?int
    {
        return $request->user()?->id ?? Auth::id();
    }
}