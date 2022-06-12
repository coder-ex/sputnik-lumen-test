<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    /**
     * The user service instance
     *
     * @var App\Http\Services\UserService
     */
    private $user_service;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user_service = new UserService();
    }

    /**
     * User registration function
     *
     * @param $request [Illuminate\Http\Request]
     * @return void
     */
    public function registration(Request $req)
    {
        try {
            $this->validate($req, [
                'email'     => 'required|min:8|max:50|email',
                'password'  => 'required|min:5|max:32|string'
            ]);

            $user_data = $this->user_service->registration($req['email'], $req['password']);
            [$token, $user] = $user_data;

            //return response()->json($user_data)->cookie('refreshToken', $token['refresh'], array('expires' => 30 * 24 * 60 * 60 * 1000, 'httponly' => true));
            setcookie(
                'refreshToken',
                $token['refresh'],
                array('expires' => intval(env('JWT_REFRESH_SECRET')) * 1000, 'httponly' => true)
            );
            //---
            return response()->json($user_data);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 400);
        }
    }

    public function login(Request $req)
    {
        try {
            $this->validate($req, [
                'email'     => 'required|min:8|max:50|email',
                'password'  => 'required|min:5|max:32|string'
            ]);

            $user_data = $this->user_service->login($req['email'], $req['password']);
            [$token, $user] = $user_data;
            //return response()->json($user_data)->cookie('refreshToken', $token['refresh'], array('expires' => 30 * 24 * 60 * 60 * 1000, 'httponly' => true));
            setcookie(
                'refreshToken',
                $token['refreshToken'],
                array('expires' => intval(env('JWT_REFRESH_SECRET')) * 1000, 'httponly' => true)
            );

            $token['user'] = $user;
            //---
            return response()->json($token);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function logout(Request $req)
    {
        try {
            $refresh_token = $req->cookie('refreshToken');
            if (is_null($refresh_token)) {
                return response()->json(['message' => 'Token уже удален.'], 401);
            }
            $token = $this->user_service->logout($refresh_token);

            setcookie('refreshToken');
            return response()->json($token);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function activate(Request $req)
    {
        try {
            if (is_null($req['link'])) {
                return response()->json(['message' => 'Нет данных link.'], 401);
            }

            $this->user_service->activate($req['link']);
            return redirect(url(env('CLIENT_URL')));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function refresh(Request $req)
    {
        try {
            $refresh_token = $req->cookie('refreshToken');
            if (is_null($refresh_token)) {
                return response()->json(['message' => 'Token уже удален.'], 401);
            }

            $user_data = $this->user_service->refresh($refresh_token);
            [$token, $user] = $user_data;
            setcookie(
                'refreshToken',
                $token['refreshToken'],
                array('expires' => intval(env('JWT_REFRESH_SECRET')) * 1000, 'httponly' => true)
            );
            $token['user'] = $user;
            //---
            return response()->json($token);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function edit(Request $req)
    {
        try {
            $this->validate($req, [
                'name'  => 'required|max:10',
                'email' => 'required|min:8|max:50|email',
                'password' => 'required|min:5|max:32|string'
            ]);

            $access_token = explode(' ', $req->headers->get('Authorization'), PHP_INT_MAX)[1];
            $user_data = [
                $access_token,
                $req['name'],
                $req['email'],
                $req['password']
            ];
            return response()->json($this->user_service->editUser(...$user_data), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function delete(Request $req)
    {
        try {
            $access_token = explode(' ', $req->headers->get('Authorization'), PHP_INT_MAX)[1];
            return response()->json($this->user_service->deleteUser($access_token), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function getUsers(Request $req)
    {
        try {
            return response()->json($this->user_service->getUsers());
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }
}
