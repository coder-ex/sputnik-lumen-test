<?php

namespace App\Http\Controllers;

use App\Events\SaveUserMatchEvent;
use App\Http\Services\LotteryService;
use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class LotteryController extends BaseController
{
    /**
     * The user service instance
     *
     * @var App\Http\Services\LotteryService
     */
    private $lotteryService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->lotteryService = new LotteryService();
    }

    public function getAllGames()
    {
        try {
            $result = $this->lotteryService->getAllGames();
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function createMatch(Request $req)
    {
        try {
            $this->validate($req, [
                'start_date' => 'required',
                'start_time' => 'required',
                'game_name' => 'required|min:5',
            ]);

            $result = $this->lotteryService->createMatch($req['start_date'], $req['start_time'], $req['game_name']);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function finishinghMatch(Request $req)
    {
        try {
            $this->validate($req, [
                'id'     => 'required',
                'finish' => 'required'
            ]);

            return response()->json($this->lotteryService->finishinghMatch($req['id'], $req['finish']));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function saveUserGame(Request $req)
    {
        try {
            $this->validate($req, [
                'match_id' => 'required',
                'user_id'  => 'required',
            ]);

            return response()->json($this->lotteryService->saveUserGame($req['user_id'], $req['match_id']));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }

    public function getMatches(Request $req)
    {
        try {
            $this->validate($req, [
                'lottery_game_id' => 'required',
            ]);
            return response()->json($this->lotteryService->getMatches($req['lottery_game_id']));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], isset($e->status) ? $e->status : 401);
        }
    }
}
