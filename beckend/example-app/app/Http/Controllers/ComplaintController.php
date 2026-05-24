<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Complaint;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    /**
     * Создание жалобы фрилансером на заказчика по проекту.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'text' => 'required|string|min:5|max:2000',
        ], [
            'text.required' => 'Опишите суть жалобы',
            'text.min' => 'Слишком короткое описание жалобы',
        ]);

        $user = $request->user() ?? Auth::user();
        $project = Project::findOrFail($request->project_id);

        $freelancerId = (int) ($project->freelancer_id ?? 0);

        // Жаловаться может только назначенный исполнитель проекта
        if (!$freelancerId || $freelancerId !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Пожаловаться может только исполнитель этого проекта',
            ], 403);
        }

        $customerId = (int) $project->customer_id;

        // защита от дубля: одна активная жалоба на проект от исполнителя
        $existing = Complaint::where('project_id', $project->id)
            ->where('freelancer_id', $user->id)
            ->where('status', Complaint::STATUS_PENDING)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже отправили жалобу по этому проекту, она на рассмотрении',
            ], 422);
        }

        $complaint = Complaint::create([
            'project_id' => $project->id,
            'freelancer_id' => $user->id,
            'customer_id' => $customerId,
            'text' => $request->text,
            'status' => Complaint::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Жалоба отправлена администрации',
            'complaint' => $complaint,
        ], 201);
    }

    /**
     * Список всех жалоб (для админа).
     */
    public function index()
    {
        $complaints = Complaint::with(['project', 'freelancer', 'customer'])
            ->orderByRaw("FIELD(status, 'pending', 'accepted', 'rejected')")
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'complaints' => $complaints,
        ]);
    }

    /**
     * Принять жалобу: создаётся чат админ ↔ фрилансер.
     */
    public function accept(Request $request, Complaint $complaint)
    {
        $adminId = (int) ($request->user()?->id ?? Auth::id());
        $freelancerId = (int) $complaint->freelancer_id;

        if (!$adminId) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось определить администратора',
            ], 401);
        }

        // Ищем существующий чат админ ↔ фрилансер по этому проекту
        $chat = Chat::where('project_id', $complaint->project_id)
            ->where(function ($q) use ($adminId, $freelancerId) {
                $q->where(function ($q2) use ($adminId, $freelancerId) {
                    $q2->where('author_id', $adminId)
                       ->where('interlocutor_id', $freelancerId);
                })->orWhere(function ($q2) use ($adminId, $freelancerId) {
                    $q2->where('author_id', $freelancerId)
                       ->where('interlocutor_id', $adminId);
                });
            })
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'author_id' => $adminId,
                'interlocutor_id' => $freelancerId,
                'project_id' => $complaint->project_id,
                'token' => (string) Str::uuid(),
            ]);
        }

        $complaint->status = Complaint::STATUS_ACCEPTED;
        $complaint->chat_id = $chat->id;
        $complaint->save();

        return response()->json([
            'success' => true,
            'message' => 'Жалоба принята, создан чат с исполнителем',
            'complaint' => $complaint->load(['project', 'freelancer', 'customer']),
            'chat_id' => $chat->id,
        ]);
    }

    /**
     * Отклонить жалобу.
     */
    public function reject(Request $request, Complaint $complaint)
    {
        $complaint->status = Complaint::STATUS_REJECTED;
        $complaint->save();

        return response()->json([
            'success' => true,
            'message' => 'Жалоба отклонена',
            'complaint' => $complaint->load(['project', 'freelancer', 'customer']),
        ]);
    }
}
