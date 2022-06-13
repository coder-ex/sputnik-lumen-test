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
        $authHeader = $req->headers->get('Authorization');
        if (is_null($authHeader)) {
            return response()->json([
                'message' => 'Пользователь не авторизован.'
            ], 401);
        }

        $accessToken = explode(' ', $authHeader, PHP_INT_MAX)[1];
        if (is_null($accessToken)) {
            return response()->json([
                'message' => 'Пользователь не авторизован.'
            ], 401);
        }

        $data = TokenService::validate($accessToken, env('JWT_ACCESS_SECRET'));
        if (is_null($data)) {
            return response()->json([
                'message' => 'Пользователь не авторизован.'
            ], 401);
        }

        //--- поместим пользователя в класс запросов, чтобы вы могли его подхватить оттуда
        $req->auth = User::find($data->sub);
        //---
        return $next($req);
    }
}
