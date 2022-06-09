<?php

namespace App\Http\Services;

use App\Models\Token;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{
    public function generate(User $user): array
    {
        $payload = [
            'iss' => "lumen-jwt",               // эмитент token
            'sub' => $user->id,                 // субъект token
            'iat' => time(),                    // время выпуска JWT 
            'exp' => time() + env('EXP_ACCESS') // экспирация access token
        ];

        $access_token = JWT::encode($payload, env('JWT_ACCESS_SECRET'), 'HS256');
        $payload['exp'] = time() + env('EXP_REFRESH');   // экспирация refresh token
        $refresh_token = JWT::encode($payload, env('JWT_REFRESH_SECRET'), 'HS256');

        //---
        return [
            'access' => $access_token,
            'refresh' => $refresh_token
        ];
    }

    public static function validate(string $token, string $secret)
    {
        try {
            return JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return null; //$e->getMessage();
        } catch (\Firebase\JWT\BeforeValidException $e) {
            return null; //$e->getMessage();
        } catch (\Firebase\JWT\ExpiredException $e) {
            return null; //$e->getMessage();
        } catch (Exception $e) {
            return null; //$e->getMessage();
        }
    }

    public function remove(string $refresh_token)
    {
        $token = Token::where('refresh_token', $refresh_token)->first();
        $token->delete();
        return $token;
    }

    public function save(int $userId, string $refresh)
    {
        $token_data = Token::where('user_id', $userId)->first();
        if (!is_null($token_data)) {
            $token_data->refresh_token = $refresh;
            return $token_data->save();
        }

        $token = new Token();
        $token->user_id = $userId;
        $token->refresh_token = $refresh;
        $token->save();

        //---
        return $token;
    }

    public function findToken(string $token)
    {
        return Token::where('refresh_token', $token)->first();
    }
}
