<?php

namespace App\Http\Middleware;

use App\Http\Services\TokenService;
use Closure;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $req, Closure $next, $guard = null)
    {
        $auth_header = $req->headers->get('Authorization');
        if (is_null($auth_header)) {
            return response()->json([
                'error' => 'Пользователь не авторизован.'
            ], 401);
        }

        $access_token = explode(' ', $auth_header, PHP_INT_MAX)[1];
        if (is_null($access_token)) {
            return response()->json([
                'error' => 'Пользователь не авторизован.'
            ], 401);
        }

        $user_data = TokenService::validate($access_token, env('JWT_ACCESS_SECRET'));
        if (is_null($user_data)) {
            return response()->json([
                'error' => 'Пользователь не авторизован.'
            ], 401);
        }

        //--- поместим пользователя в класс запросов, чтобы вы могли его подхватить оттуда
        $req->auth = User::find($user_data->sub);
        $t = $req->auth;
        //---
        return $next($req);
    }
}
