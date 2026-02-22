<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function registred()
    {
        $user = User::create([
            'full_name' => request('full_name'),
            'phone' => request('phone'),
            'login' => request('login'),
            'avatar' => request('avatar'),
            'password' => Hash::make(request('password')),
            'role' => request('role') == 'фрилансер' ? 'freelancer' : 'customer',
            'balance' => 0.00,
            'rating' => 0.00,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Регистрация успешна'
        ]);
    }
}
