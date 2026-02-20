<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        try {
            $success = auth()->attempt([
                'login' => request('login'),
                'password' => request('password')
            ], request()->has('remember'));

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Вход успешен'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Неверный логин или пароль'
            ], 401);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка авторизации'
            ], 400);
        }
    }
}
