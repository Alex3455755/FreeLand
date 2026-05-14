<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/logout') && $request->isMethod('post')) {
            return $next($request);
        }

        $user = $request->user();
        if ($user && $user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'Ваш аккаунт заблокирован. Обратитесь в поддержку.',
            ], 403);
        }

        return $next($request);
    }
}
