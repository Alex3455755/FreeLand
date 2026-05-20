<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FreeLandMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function __construct(
        private readonly FreeLandMailService $mailService
    ) {
    }

    public function registred()
    {
        request()->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'email', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string'],
            'agreed_to_terms' => ['accepted'],
        ], [
            'agreed_to_terms.accepted' => 'Необходимо принять пользовательское соглашение',
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

        try {
            $this->mailService->sendVerificationCode($user->login, $verificationCode);
        } catch (\Exception $e) {
            $user->delete();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'requires_verification' => true,
            'message' => 'На email отправлен код подтверждения',
            'login' => $user->login,
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'login' => ['required', 'email'],
            'code' => ['required', 'string'],
        ]);

        $code = preg_replace('/\D/', '', $request->code);
        if (strlen($code) !== 6) {
            return response()->json([
                'success' => false,
                'message' => 'Введите 6-значный код',
            ], 422);
        }

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
                'message' => 'Email уже подтверждён',
            ]);
        }

        if (
            !$user->email_verification_code
            || !hash_equals((string) $user->email_verification_code, $code)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код',
            ], 422);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Email подтверждён, можно войти',
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

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Этот email уже подтверждён',
            ], 422);
        }

        $verificationCode = (string) random_int(100000, 999999);
        $user->update([
            'email_verification_code' => $verificationCode,
        ]);

        try {
            $this->mailService->sendVerificationCode($user->login, $verificationCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Новый код отправлен на email',
        ]);
    }
}
