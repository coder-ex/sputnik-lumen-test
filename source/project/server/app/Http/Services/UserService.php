<?php

namespace App\Http\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $token_service;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->token_service = new TokenService();
    }

    public function registration(string $email, string $password)
    {
        $candidate = User::where('email', $email)->first();
        if (!is_null($candidate)) {
            return ['error' => 'Пользователь с E-Mail: ' . $email . ' уже существует.'];
        }

        $hash_password = Hash::make($password);
        $activation_link = uniqid();

        $user = new User();
        $user->email = $email;
        $user->password = $hash_password;
        $user->activation_link = $activation_link;
        $user->save();

        $tokens = $this->token_service->generate($user);
        $this->token_service->save($user->id, $tokens['refresh']);
        //---
        return [$tokens, $user];
    }

    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            return ['error' => 'Пользователь с E-Mail: ' . $email . ' не существует.'];
        }

        $hash_pass = Hash::check($password, $user->password);
        if (!$hash_pass) {
            return ['error' => 'Не верный пароль.'];
        }

        $tokens = $this->token_service->generate($user);
        $this->token_service->save($user->id, $tokens['refresh']);
        //---
        return [$tokens, $user];
    }

    public function logout(string $refresh_token)
    {
        return $this->token_service->remove($refresh_token);
    }

    public function refresh(string $refresh_token)
    {
        $user_data = TokenService::validate($refresh_token, env('JWT_REFRESH_SECRET'));
        $token_from_db = $this->token_service->findToken($refresh_token);

        if (is_null($user_data) || is_null($token_from_db)) {
            return ['error' => 'Пользователь не авторизован.'];
        }

        $user = User::where('id', $user_data->sub)->first();
        $tokens = $this->token_service->generate($user);
        $this->token_service->save($user->id, $tokens['refresh']);
        //---
        return [$tokens, $user];
    }

    public function editUser($token, $name, $email, $password)
    {
        $user_data = TokenService::validate($token, env('JWT_ACCESS_SECRET'));
        $user = User::where('id', $user_data->sub)->first();
        $user->name = $name ? $name : $user->name;
        $user->email = $email ? $email : $user->email;
        $user->password = $password ? Hash::make($password) : $user->password;
        $user->save();
        $user->tokens;
        return $user;
    }

    public function deleteUser(string $token)
    {
        $user_data = TokenService::validate($token, env('JWT_ACCESS_SECRET'));
        $user = User::where('id', $user_data->sub)->first();
        $user->delete();
        return $user;
    }

    public function getUsers()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->matches;
        }
        return $users;
    }
}
