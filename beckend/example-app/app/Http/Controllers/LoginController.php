<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Вход пользователя через SPA
     */
    public function login(Request $request)
    {
        // Логируем запрос для отладки
        Log::info('Login attempt', ['data' => $request->all()]);
        
        try {
            // Валидация
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            $credentials = [
                'login' => $request->login,
                'password' => $request->password,
            ];

            // Попытка авторизации
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                // Регенирация сессии после логина
                $request->session()->regenerate();

                $user = Auth::user();
                
                Log::info('Login successful', ['user_id' => $user->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Авторизация успешна',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'login' => $user->login,
                        'email' => $user->email
                    ]
                ]);
            }

            // Если логин или пароль неверные
            Log::warning('Login failed - invalid credentials');
            
            return response()->json([
                'success' => false,
                'message' => 'Неверный логин или пароль'
            ], 401);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Login validation error', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Login error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера'
            ], 500);
        }
    }
}