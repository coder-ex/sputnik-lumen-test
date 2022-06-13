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
        $this->userService = new UserService();
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

            $data = $this->userService->registration($req['email'], $req['password']);

            setcookie(
                'refreshToken',
                $data['refreshToken'],
                array('expires' => intval(env('JWT_REFRESH_SECRET')) * 1000, 'httponly' => true/*, 'SameSite' => 'None', 'Secure' => true*/)
            );

            //---
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 422);
        }
    }

    public function login(Request $req)
    {
        try {
            $this->validate($req, [
                'email'     => 'required|min:8|max:50|email',
                'password'  => 'required|min:5|max:32|string'
            ]);

            $data = $this->userService->login($req['email'], $req['password']);

            setcookie(
                'refreshToken',
                $data['refreshToken'],
                array('expires' => intval(env('JWT_REFRESH_SECRET')) * 1000, 'httponly' => true/*, 'SameSite' => 'None', 'Secure' => true*/)
            );

            //---
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
        }
    }

    public function logout(Request $req)
    {
        try {
            $refreshToken = $req->cookie('refreshToken');
            if (is_null($refreshToken)) {
                throw new Exception('Пользователь не авторизован.', 401);
            }
            $token = $this->userService->logout($refreshToken);

            setcookie('refreshToken');
            return response()->json($token);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
        }
    }

    public function activate(Request $req)
    {
        try {
            if (is_null($req['link'])) {
                throw new Exception('Нет данных link.', 400);
            }

            $this->userService->activate($req['link']);
            return redirect(url(env('CLIENT_URL')), 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
        }
    }

    public function refresh(Request $req)
    {
        try {
            $refreshToken = $req->cookie('refreshToken');
            if (is_null($refreshToken)) {
                throw new Exception('Пользователь не авторизован.', 401);
            }

            $data = $this->userService->refresh($refreshToken);

            setcookie(
                'refreshToken',
                $data['refreshToken'],
                array('expires' => intval(env('JWT_REFRESH_SECRET')) * 1000, 'httponly' => true/*, 'SameSite' => 'None', 'Secure' => true*/)
            );

            //---
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
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

            $accessToken = explode(' ', $req->headers->get('Authorization'), PHP_INT_MAX)[1];
            $data = [
                $accessToken,
                $req['name'],
                $req['email'],
                $req['password']
            ];

            //---
            return response()->json($this->userService->editUser(...$data), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
        }
    }

    public function delete(Request $req)
    {
        try {
            $accessToken = explode(' ', $req->headers->get('Authorization'), PHP_INT_MAX)[1];
            return response()->json($this->userService->deleteUser($accessToken), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
        }
    }

    public function getUsers(Request $req)
    {
        try {
            return response()->json($this->userService->getUsers());
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ? $e->getCode() : 401);
        }
    }
}
