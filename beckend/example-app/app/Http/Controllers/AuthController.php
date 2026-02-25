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
                'role' => $request->user()->role,
                'avatar' => $request->user()->avatar ?? null,
                'balance' => $request->user()->balance?? null
            ] : null
        ]);
    }
}