<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'full_name' => $request->user()->full_name,
                'login' => $request->user()->login,
                'phone' => $request->user()->phone ?? null,
                'role' => $request->user()->role,
                'avatar' => $request->user()->avatar ?? null,
                'balance' => $request->user()->balance ?? null,
                'is_banned' => $request->user()->isBanned(),
                'telegram' => $request->user()->telegram ?? null,
                'github' => $request->user()->github ?? null,
                'portfolio_url' => $request->user()->portfolio_url ?? null,
                'website' => $request->user()->website ?? null,
            ] : null
        ]);
    }
}