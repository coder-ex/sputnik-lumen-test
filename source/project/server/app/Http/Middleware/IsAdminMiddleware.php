<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;

class IsAdminMiddleware
{
    public function handle(Request $req, Closure $next, $guard = null)
    {
        if (!$req->auth->is_admin) {
            return response()->json([
                'error' => 'Пользователь ' . $req->auth->email . ' не администратор.'
            ], 401);
        }

        return $next($req);
    }
}
