<?php

namespace App\Http\Services;

use App\Mail\VerifyMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserService
{
    private $tokenService;
    private $mailService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tokenService = new TokenService();
        $this->mailService = new MailService();
    }

    public function registration(string $email, string $password)
    {
        $candidate = User::where('email', $email)->first();
        if (!is_null($candidate)) {
            throw new Exception('Пользователь с E-Mail: ' . $email . ' уже существует.', 400);
        }

        $hashPassword = Hash::make($password);
        $activationLink = Uuid::uuid4();

        $user = new User();
        $user->email = $email;
        $user->password = $hashPassword;
        $user->activation_link = $activationLink;
        $user->save();

        //--- to mail
        $this->mailService->sendActivationMail(new VerifyMail($user), $email, $activationLink);

        $data = $this->tokenService->generate($user);
        $this->tokenService->save($user->id, $data['refreshToken']);
        $data['user'] = $user;
        //---
        return $data;
    }

    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            throw new Exception('Пользователь с E-Mail: ' . $email . ' не существует.', 400);
        }

        $hashPass = Hash::check($password, $user->password);
        if (!$hashPass) {
            throw new Exception('Не верный пароль.', 400);
        }

        if (!$user->is_activated) {
            throw new Exception('Пользователь ' . $email . ' не подтвердил E-Mail.', 401);
        }

        $data = $this->tokenService->generate($user);
        $this->tokenService->save($user->id, $data['refreshToken']);
        $data['user'] = $user;
        //---
        return $data;
    }

    public function logout(string $refreshToken)
    {
        return $this->tokenService->remove($refreshToken);
    }

    public function activate(string $link)
    {
        $user = User::where('activation_link', $link)->first();
        if (is_null($user)) {
            throw new Exception('Линк активации не валидный.', 401);
        }

        if ($user->is_activated) {
            throw new Exception('Пользователь уже активирован.', 202);
        }

        $user->is_activated = true;
        $user->save();
    }

    public function refresh(string $refreshToken)
    {
        $data = TokenService::validate($refreshToken, env('JWT_REFRESH_SECRET'));
        $token = $this->tokenService->findToken($refreshToken);

        if (is_null($data) || is_null($token)) {
            throw new Exception('Пользователь не авторизован.');
        }

        $user = User::where('id', $data->sub)->first();
        $data = $this->tokenService->generate($user);
        $this->tokenService->save($user->id, $data['refreshToken']);
        $data['user'] = $user;
        //---
        return $data;
    }

    /**
     * редактирование данных пользователя
     *
     * @param [type] $token
     * @param [type] $name
     * @param [type] $email
     * @param [type] $password
     * @return User
     */
    public function editUser($token, $name, $email, $password): User
    {
        $data = TokenService::validate($token, env('JWT_ACCESS_SECRET'));
        $user = User::where('id', $data->sub)->first();
        $user->name = $name ? $name : $user->name;
        $user->email = $email ? $email : $user->email;
        $user->password = $password ? Hash::make($password) : $user->password;
        $user->save();
        $user->tokens;
        //---
        return $user;
    }

    public function deleteUser(string $token)
    {
        $data = TokenService::validate($token, env('JWT_ACCESS_SECRET'));
        $user = User::where('id', $data->sub)->first();
        $user->delete();
        //---
        return $user;
    }

    public function getUsers()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->matches;
        }
        //---
        return $users;
    }
}
