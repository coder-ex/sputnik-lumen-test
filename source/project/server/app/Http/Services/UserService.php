<?php

namespace App\Http\Services;

use App\Mail\VerifyMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserService
{
    private $token_service;
    private $mail_service;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->token_service = new TokenService();
        $this->mail_service = new MailService();
    }

    public function registration(string $email, string $password)
    {
        $candidate = User::where('email', $email)->first();
        if (!is_null($candidate)) {
            throw new Exception('Пользователь с E-Mail: ' . $email . ' уже существует.');
        }

        $hash_password = Hash::make($password);
        $activation_link = Uuid::uuid4();

        $user = new User();
        $user->email = $email;
        $user->password = $hash_password;
        $user->activation_link = $activation_link;
        $user->save();

        //--- to mail
        $this->mail_service->sendActivationMail(new VerifyMail($user), $email, $activation_link);

        $tokens = $this->token_service->generate($user);
        $this->token_service->save($user->id, $tokens['refreshToken']);
        //---
        return [$tokens, $user];
    }

    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            throw new Exception('Пользователь с E-Mail: ' . $email . ' не существует.');
        }

        $hash_pass = Hash::check($password, $user->password);
        if (!$hash_pass) {
            throw new Exception('Не верный пароль.');
        }

        $tokens = $this->token_service->generate($user);
        $this->token_service->save($user->id, $tokens['refreshToken']);
        //---
        return [$tokens, $user];
    }

    public function logout(string $refresh_token)
    {
        return $this->token_service->remove($refresh_token);
    }

    public function activate(string $link)
    {
        $user = User::where('activation_link', $link)->first();
        if (is_null($user)) {
            throw new Exception('Линк активации не валидный.');
        }

        if ($user->is_activated) {
            throw new Exception('Пользователь уже активирован.');
        }

        $user->is_activated = true;
        $user->save();
    }

    public function refresh(string $refresh_token)
    {
        $user_data = TokenService::validate($refresh_token, env('JWT_REFRESH_SECRET'));
        $token_from_db = $this->token_service->findToken($refresh_token);

        if (is_null($user_data) || is_null($token_from_db)) {
            throw new Exception('Пользователь не авторизован.');
        }

        $user = User::where('id', $user_data->sub)->first();
        $tokens = $this->token_service->generate($user);
        $this->token_service->save($user->id, $tokens['refreshToken']);
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
