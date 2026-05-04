<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use App\Models\User;

class ApplicationController extends Controller
{
    // Временно отключено: отправка email уведомлений
    // private PhpMailerService $mailer;
    //
    // public function __construct(PhpMailerService $mailer)
    // {
    //     $this->mailer = $mailer;
    // }
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

        $mail = new PHPMailer(true);
        $user = User::find($freelancerId);

        try {
            $mail->SMTPDebug = 2;

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alexeigyll@gmail.com';
            $mail->Password = 'wjdd rplm hkhi ijhp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom('alexeigyll@gmail.com', 'freeland');
            $mail->addAddress($user->login);

            $mail->isHTML(true);
            $mail->Subject = 'Отклик по заявке';
            $mail->Body = "Ваша заявка по проекту принята";

            $mail->SMTPDebug = 2;
            $mail->Timeout = 10;
            $mail->Debugoutput = function($str, $level) {
                error_log("SMTP: $str");
            };

            if (!$mail->send()) {
                throw new \Exception($mail->ErrorInfo);
            }

            return response()->json([
                'success' => true,
                'message' => 'Письмо отправлено'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        // Временно отключено: отправка email уведомлений
        // $this->sendApplicationStatusEmail($application, true);

        // Синхронизируем проект с принятым исполнителем
        $projectUpdateData = [
            'status' => $project->status === 'open' ? 'in_progress' : $project->status,
        ];
        if (Schema::hasColumn('projects', 'freelancer_id')) {
            $projectUpdateData['freelancer_id'] = $freelancerId;
        }
        if (Schema::hasColumn('projects', 'frelancer_id')) {
            $projectUpdateData['frelancer_id'] = $freelancerId;
        }

        $project->update($projectUpdateData);

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

        // Временно отключено: отправка email уведомлений
        // $this->sendApplicationStatusEmail($application, false);
        $freelancerId = $application->user_id;
        $mail = new PHPMailer(true);
        $user = User::find($freelancerId);
        

        try {
            $mail->SMTPDebug = 2;

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alexeigyll@gmail.com';
            $mail->Password = 'wjdd rplm hkhi ijhp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom('alexeigyll@gmail.com', 'freeland');
            $mail->addAddress($user->login);

            $mail->isHTML(true);
            $mail->Subject = 'Отклик по заявке';
            $mail->Body = "Ваша заявка по проекту отклоненна";

            $mail->SMTPDebug = 2;
            $mail->Timeout = 10;
            $mail->Debugoutput = function($str, $level) {
                error_log("SMTP: $str");
            };

            if (!$mail->send()) {
                throw new \Exception($mail->ErrorInfo);
            }

            return response()->json([
                'success' => true,
                'message' => 'Письмо отправлено'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

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

    // Временно отключено: отправка email уведомлений
    // private function sendApplicationStatusEmail(Application $application, bool $accepted): void
    // {
    //     $application->loadMissing(['user', 'project']);
    //     $freelancer = $application->user;
    //     if (!$freelancer || !$freelancer->login) {
    //         return;
    //     }
    //
    //     $projectTitle = $application->project?->title ?: ('#' . $application->project_id);
    //     $name = $freelancer->full_name ?: $freelancer->login;
    //     $subject = $accepted ? 'Ваша заявка принята' : 'Ваша заявка отклонена';
    //     $statusText = $accepted ? 'принята' : 'отклонена';
    //
    //     $this->mailer->send(
    //         $freelancer->login,
    //         $name,
    //         $subject,
    //         "Здравствуйте, {$name}!<br><br>Ваша заявка по проекту <b>{$projectTitle}</b> была {$statusText}."
    //     );
    // }
}