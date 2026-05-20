<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FreeLandMailService;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly FreeLandMailService $mailService
    ) {
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'login' => ['required', 'email'],
        ]);

        $user = User::where('login', $request->login)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь с таким email не найден',
            ], 404);
        }

        if ($user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'Аккаунт заблокирован',
            ], 403);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Сначала подтвердите email при регистрации',
            ], 422);
        }

        $code = (string) random_int(100000, 999999);
        $user->update([
            'password_reset_code' => $code,
        ]);

        try {
            $this->mailService->sendPasswordResetCode($user->login, $code);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Код для входа отправлен на email',
            'login' => $user->login,
        ]);
    }

    public function loginWithCode(Request $request)
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

        if ($user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'Аккаунт заблокирован',
            ], 403);
        }

        if (
            !$user->password_reset_code
            || !hash_equals((string) $user->password_reset_code, $code)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или устаревший код',
            ], 422);
        }

        $user->forceFill([
            'password_reset_code' => null,
        ])->save();

        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'login' => $user->login,
                'role' => $user->role,
                'avatar' => $user->avatar ?? null,
            ],
            'message' => 'Вход выполнен',
        ]);
    }
}
