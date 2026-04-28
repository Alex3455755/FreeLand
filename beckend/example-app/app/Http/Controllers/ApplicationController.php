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

        $userId = Auth::id();

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
    public function accept(Application $application)
    {
        $this->authorizeProjectOwner($application);

        $application->update([
            'status' => Application::STATUS_ACCEPTED,
        ]);

        // 🔥 создаём чат при принятии заявки
        $chat = Chat::firstOrCreate([
            'author_id' => $application->project->user_id,
            'interlocutor_id' => $application->user_id,
            'project_id' => $application->project_id,
        ], [
            'token' => Str::uuid(),
        ]);

        return response()->json([
            'application' => $application,
            'chat' => $chat,
        ]);
    }

    /**
     * Отклонить заявку
     */
    public function reject(Application $application)
    {
        $this->authorizeProjectOwner($application);

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
    public function destroy(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $application->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Проверка прав владельца проекта
     */
    private function authorizeProjectOwner(Application $application)
    {
        if ($application->project->user_id !== Auth::id()) {
            abort(403, 'Only project owner can change status');
        }
    }
}