<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    // Временно отключено: отправка email при регистрации
    // private PhpMailerService $mailer;
    //
    // public function __construct(PhpMailerService $mailer)
    // {
    //     $this->mailer = $mailer;
    // }

    public function registred()
    {
        request()->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'email', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string'],
        ]);

        // Временно отключено: email-верификация при регистрации
        // $verificationCode = (string) random_int(100000, 999999);

        $user = User::create([
            'full_name' => request('full_name'),
            'phone' => request('phone'),
            'login' => request('login'),
            'avatar' => request('avatar'),
            'password' => Hash::make(request('password')),
            'email_verification_code' => null,
            'email_verified_at' => now(),
            'role' => request('role') == 'фрилансер' ? 'freelancer' : 'customer',
            'balance' => 0.00,
            'rating' => 0.00,
        ]);

        // Временно отключено: отправка кода на email
        // $name = $user->full_name ?: 'Пользователь';
        // $this->mailer->send(
        //     $user->login,
        //     $name,
        //     'Код подтверждения FreeLand',
        //     "Здравствуйте, {$name}!<br><br>Ваш код подтверждения: <b>{$verificationCode}</b><br><br>Если вы не регистрировались, просто проигнорируйте это письмо."
        // );

        return response()->json([
            'success' => true,
            'requires_verification' => false,
            'message' => 'Регистрация успешно завершена',
            'login' => $user->login,
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Подтверждение email временно отключено',
        ]);
    }

    public function resendEmailCode(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Отправка кода временно отключена',
        ]);
    }
}
