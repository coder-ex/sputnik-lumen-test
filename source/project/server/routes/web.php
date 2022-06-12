<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('api/users/register', ['uses' => 'UserController@registration']);
$router->post('api/users/login', ['as' => 'login', 'uses' => 'UserController@login']);
$router->get('api/users/logout', ['uses' => 'UserController@logout']);
$router->get('api/users/activate/{link}', ['uses' => 'UserController@activate']);
$router->get('api/users/refresh', ['uses' => 'UserController@refresh']);
$router->get('api/lottery_games', ['uses' => 'LotteryController@getAllGames']);
$router->get('api/lottery_game_matchs', ['uses' => 'LotteryController@getMatches']);

$router->group(
    ['middleware' => ['jwt.auth', 'admin.auth']],
    function () use ($router) {
        $router->post('api/lottery_game_matchs', ['uses' => 'LotteryController@createMatch']);
        $router->put('api/lottery_game_matchs', ['uses' => 'LotteryController@finishinghMatch']);
        $router->get('api/users', ['uses' => 'UserController@getUsers']);
    }
);

$router->group(
    ['middleware' => 'jwt.auth'],
    function () use ($router) {
        $router->put('api/users/{id}', ['uses' => 'UserController@edit']);
        $router->delete('api/users/{id}', ['uses' => 'UserController@delete']);
        $router->post('api/lottery_game_match_users', ['uses' => 'LotteryController@saveUserGame']);
    }
);
