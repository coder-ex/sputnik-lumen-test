<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;

class IsAdminMiddleware
{
    public function handle(Request $req, Closure $next, $guard = null)
    {
        if (!$req->auth->is_admin) {
            return response()->json([
                'message' => 'Пользователь ' . $req->auth->email . ' не администратор.'
            ], 403);
        }

        return $next($req);
    }
}
