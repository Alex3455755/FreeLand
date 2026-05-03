<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\PhpMailerService;

class RegisterController extends Controller
{
    private PhpMailerService $mailer;

    public function __construct(PhpMailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public function registred()
    {
        request()->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'email', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string'],
        ]);

        $verificationCode = (string) random_int(100000, 999999);

        $user = User::create([
            'full_name' => request('full_name'),
            'phone' => request('phone'),
            'login' => request('login'),
            'avatar' => request('avatar'),
            'password' => Hash::make(request('password')),
            'email_verification_code' => $verificationCode,
            'email_verified_at' => null,
            'role' => request('role') == 'фрилансер' ? 'freelancer' : 'customer',
            'balance' => 0.00,
            'rating' => 0.00,
        ]);

        $name = $user->full_name ?: 'Пользователь';
        $this->mailer->send(
            $user->login,
            $name,
            'Код подтверждения FreeLand',
            "Здравствуйте, {$name}!<br><br>Ваш код подтверждения: <b>{$verificationCode}</b><br><br>Если вы не регистрировались, просто проигнорируйте это письмо."
        );

        return response()->json([
            'success' => true,
            'requires_verification' => true,
            'message' => 'Код подтверждения отправлен на email',
            'login' => $user->login,
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'login' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('login', $request->login)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email уже подтвержден',
            ]);
        }

        if ($user->email_verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код подтверждения',
            ], 422);
        }

        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email успешно подтвержден',
        ]);
    }

    public function resendEmailCode(Request $request)
    {
        $request->validate([
            'login' => ['required', 'email'],
        ]);

        $user = User::where('login', $request->login)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 404);
        }

        $verificationCode = (string) random_int(100000, 999999);
        $user->email_verification_code = $verificationCode;
        $user->save();

        $name = $user->full_name ?: 'Пользователь';
        $this->mailer->send(
            $user->login,
            $name,
            'Новый код подтверждения FreeLand',
            "Здравствуйте, {$name}!<br><br>Ваш новый код подтверждения: <b>{$verificationCode}</b>"
        );

        return response()->json([
            'success' => true,
            'message' => 'Новый код подтверждения отправлен на email',
        ]);
    }
}
