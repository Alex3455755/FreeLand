<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /**
     * Все заявки пользователя
     */
    public function index()
    {
        return Application::with(['user', 'project'])
            ->latest()
            ->get();
    }

    /**
     * Подать заявку
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $user = $request->user();

        $userId = $user->id;

        $existing = Application::where('user_id', $userId)
            ->where('project_id', $request->project_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Application already exists'
            ], 409);
        }

        $application = Application::create([
            'user_id' => $userId,
            'project_id' => $request->project_id,
            'status' => Application::STATUS_PENDING,
        ]);

        return response()->json($application, 201);
    }

    /**
     * Принять заявку
     */
    public function accept(Request $request, Application $application)
    {
        $this->authorizeProjectOwner($request, $application);

        $project = $application->project;
        $projectOwnerId = $project->customer_id ?? $project->user_id ?? null;
        $freelancerId = $application->user_id;

        $application->update([
            'status' => Application::STATUS_ACCEPTED,
        ]);

        // Синхронизируем проект с принятым исполнителем
        $project->update([
            'freelancer_id' => $freelancerId,
            'status' => $project->status === 'open' ? 'in_progress' : $project->status,
        ]);

        // Создаём или исправляем чат проекта, чтобы он был виден обеим сторонам
        $chat = Chat::firstOrNew([
            'project_id' => $application->project_id,
        ]);
        $chat->author_id = $projectOwnerId;
        $chat->interlocutor_id = $freelancerId;
        if (!$chat->token) {
            $chat->token = Str::uuid();
        }
        $chat->save();

        return response()->json([
            'application' => $application,
            'chat' => $chat,
        ]);
    }

    /**
     * Отклонить заявку
     */
    public function reject(Request $request, Application $application)
    {
        $this->authorizeProjectOwner($request, $application);

        $application->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        return response()->json($application);
    }

    /**
     * Показ заявки
     */
    public function show(Application $application)
    {
        return $application->load(['user', 'project']);
    }

    /**
     * Удаление заявки
     */
    public function destroy(Request $request, Application $application)
    {
        $userId = $this->resolveUserId($request);
        if ($application->user_id !== $userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $application->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Проверка прав владельца проекта
     */
    private function authorizeProjectOwner(Request $request, Application $application)
    {
        $userId = $this->resolveUserId($request);
        $project = $application->project;
        $projectOwnerId = $project->customer_id ?? $project->user_id ?? null;

        if ((int) $projectOwnerId !== (int) $userId) {
            abort(403, 'Only project owner can change status');
        }
    }

    private function resolveUserId(Request $request): ?int
    {
        return $request->user()?->id ?? Auth::id();
    }
}