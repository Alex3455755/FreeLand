<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->get();

        return response()->json($projects);
    }

    public function myProjects(Request $request, User $user)
    {
        try {

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован',
                ], 401);
            }

            Log::info('Загрузка проектов для пользователя: '.$user->id.', роль: '.$user->role);

            $attachPaymentFlags = function ($projects) {
                $projects->each(function ($project) {
                    $p = $project->payments;
                    $project->setAttribute('has_paid_transfer', $p && $p->status === 'paid');
                    $project->setAttribute('has_escrow_hold', $p && $p->status === 'frozen');
                });
            };

            // Для заказчика
            if ($user->role === 'customer' || $user->role === 'заказчик') {
                $projects = Project::where('customer_id', $user->id)
                    ->with(['customer', 'freelancer', 'category', 'payments'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $attachPaymentFlags($projects);

                return response()->json([
                    'success' => true,
                    'projects' => $projects,
                ]);
            }

            // Для фрилансера
            if ($user->role === 'freelancer' || $user->role === 'фрилансер') {
                $projectsQuery = Project::query();
                $projectsQuery->where(function ($query) use ($user) {
                    $hasFilter = false;
                    if (Schema::hasColumn('projects', 'freelancer_id')) {
                        $query->where('freelancer_id', $user->id);
                        $hasFilter = true;
                    }
                    if (Schema::hasColumn('projects', 'frelancer_id')) {
                        if ($hasFilter) {
                            $query->orWhere('frelancer_id', $user->id);
                        } else {
                            $query->where('frelancer_id', $user->id);
                        }
                    }
                });

                $projects = $projectsQuery
                    ->with(['customer', 'freelancer', 'category', 'payments'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $attachPaymentFlags($projects);

                return response()->json([
                    'success' => true,
                    'projects' => $projects,
                ]);
            }

            // Для админа
            if ($user->role === 'admin') {
                $projects = Project::with(['customer', 'freelancer', 'category', 'payments'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $attachPaymentFlags($projects);

                return response()->json([
                    'success' => true,
                    'projects' => $projects,
                ]);
            }

            return response()->json([
                'success' => true,
                'projects' => [],
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при загрузке моих проектов: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке проектов: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация',
            ], 401);
        }

        $budget = (float) $request->input('budget', 0);
        if ($budget <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Укажите бюджет больше нуля',
            ], 400);
        }

        try {
            DB::transaction(function () use ($request, $user, $budget) {
                $customer = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

                if ((float) $customer->balance < $budget) {
                    throw new \RuntimeException('Недостаточно средств: бюджет резервируется при создании заказа');
                }

                $projectData = [
                    'title' => $request['title'],
                    'description' => $request['description'],
                    'budget' => $budget,
                    'deadline' => $request['deadline'],
                    'status' => 'open',
                    'category_id' => $request['category_id'],
                    'customer_id' => $customer->id,
                ];
                if (Schema::hasColumn('projects', 'freelancer_id')) {
                    $projectData['freelancer_id'] = null;
                }
                if (Schema::hasColumn('projects', 'frelancer_id')) {
                    $projectData['frelancer_id'] = null;
                }

                $customer->balance = (float) $customer->balance - $budget;
                $customer->save();

                $project = new Project($projectData);
                $project->save();

                Payment::create([
                    'customer_id' => $customer->id,
                    'freelancer_id' => $customer->id,
                    'project_id' => $project->id,
                    'amount' => $budget,
                    'commission' => 0,
                    'status' => 'frozen',
                    'type' => 'transfer',
                ]);
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Ошибка создания проекта: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Не удалось создать проект',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'проект успешно создан',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return response()->json([
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $project = Project::find($request->id);
        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'проект не найден',
            ], 404);
        }

        $oldFreelancerId = (int) ($project->freelancer_id ?? 0);

        $project->update($request->except(['id']));
        $project->refresh();

        $newFreelancerId = (int) ($project->freelancer_id ?? 0);

        if ($oldFreelancerId !== $newFreelancerId) {
            Chat::purgeMessagesForProject($project->id);
            $chat = Chat::where('project_id', $project->id)->first();
            if ($chat) {
                $chat->interlocutor_id = $newFreelancerId ?: null;
                $chat->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'проект успешно обновлен',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация',
            ], 401);
        }

        if ((int) $project->customer_id !== (int) $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Нет прав на удаление проекта',
            ], 403);
        }

        return $this->refundEscrowAndDeleteProject($project);
    }

    /**
     * Отмена заказа заказчиком: возврат зарезервированных средств.
     */
    public function cancel(Request $request, Project $project)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация',
            ], 401);
        }

        if ((int) $project->customer_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Отменить заказ может только заказчик',
            ], 403);
        }

        return $this->refundEscrowAndDeleteProject($project);
    }

    public function pay(Project $project)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован',
            ], 401);
        }

        if ((int) $project->customer_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Оплачивать проект может только заказчик',
            ], 403);
        }

        $freelancerId = $this->resolveFreelancerId($project);
        if (! $freelancerId) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя оплатить проект без назначенного исполнителя',
            ], 400);
        }

        $amount = (float) $project->budget;
        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'У проекта не указан корректный бюджет',
            ], 400);
        }

        $alreadyPaid = Payment::where('project_id', $project->id)
            ->where('type', 'transfer')
            ->where('status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return response()->json([
                'success' => false,
                'message' => 'Этот проект уже оплачен',
            ], 400);
        }

        try {
            DB::transaction(function () use ($project, $user, $amount, $freelancerId) {
                $escrow = Payment::where('project_id', $project->id)
                    ->where('status', 'frozen')
                    ->lockForUpdate()
                    ->first();

                if ($escrow) {
                    if (abs((float) $escrow->amount - $amount) > 0.009) {
                        throw new \RuntimeException('Сумма эскроу не совпадает с бюджетом проекта');
                    }

                    $freelancer = User::where('id', $freelancerId)->lockForUpdate()->firstOrFail();

                    $escrow->freelancer_id = $freelancer->id;
                    $escrow->status = 'paid';
                    $escrow->save();

                    $freelancer->balance = (float) $freelancer->balance + $amount;
                    $freelancer->save();

                    return;
                }

                // Старые проекты без эскроу: списание с баланса заказчика при оплате
                $customer = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
                if ((float) $customer->balance < $amount) {
                    throw new \RuntimeException('Недостаточно средств для оплаты проекта');
                }

                $freelancer = User::where('id', $freelancerId)->lockForUpdate()->firstOrFail();

                $customer->balance = (float) $customer->balance - $amount;
                $freelancer->balance = (float) $freelancer->balance + $amount;
                $customer->save();
                $freelancer->save();

                Payment::create([
                    'customer_id' => $customer->id,
                    'freelancer_id' => $freelancer->id,
                    'project_id' => $project->id,
                    'amount' => $amount,
                    'commission' => 0,
                    'status' => 'paid',
                    'type' => 'transfer',
                ]);
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Ошибка оплаты проекта: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Не удалось выполнить оплату',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Оплата проекта выполнена успешно',
        ]);
    }

    private function refundEscrowAndDeleteProject(Project $project)
    {
        try {
            DB::transaction(function () use ($project) {
                $project->refresh();

                $payment = Payment::where('project_id', $project->id)->lockForUpdate()->first();

                if (! $payment) {
                    $project->delete();

                    return;
                }

                if ($payment->status === 'paid') {
                    throw new \RuntimeException('Нельзя отменить проект после перевода средств исполнителю');
                }

                if ($payment->status === 'frozen') {
                    $customer = User::where('id', $payment->customer_id)->lockForUpdate()->firstOrFail();
                    $customer->balance = (float) $customer->balance + (float) $payment->amount;
                    $customer->save();

                    $payment->status = 'refunded';
                    $payment->save();
                }

                $project->delete();
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Ошибка отмены проекта: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Не удалось отменить проект',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Заказ отменён, средства возвращены на баланс',
        ]);
    }

    private function resolveFreelancerId(Project $project): ?int
    {
        $current = $project->freelancer_id ?? null;
        if ($current) {
            return (int) $current;
        }

        if (Schema::hasColumn('projects', 'frelancer_id')) {
            $legacy = $project->getAttribute('frelancer_id');
            if ($legacy) {
                return (int) $legacy;
            }
        }

        return null;
    }
}
