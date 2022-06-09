<?php

namespace App\Http\Services;

use App\Events\Event;
use App\Events\FinishMatchEvent;
use App\Events\SaveUserMatchEvent;
use App\Models\GameLottery;
use App\Models\MatchLottery;
use App\Models\User;
use DateTime;

class LotteryService
{
    public function getAllGames()
    {
        $games = GameLottery::all();
        foreach ($games as $game) {
            $game->matches;
        }

        return $games;
    }

    public function createMatch(string $startDate, string $startTime, string $nameGame)
    {
        $current_date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' 00:00:00');
        $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $startDate . ' 00:00:00');
        if ($current_date > $start_date) {
            return ['status' => 'Дата начала старта матча не может быть раньше сегодня.'];
        }

        $start_time = DateTime::createFromFormat('H:i:s', $startTime);
        $match_t = MatchLottery::where('start_time', $start_time)->first();
        if (!is_null($match_t)) {
            if ($match_t->start_time === $start_time->format('Y-m-d H:i:s') && $match_t->game->name === $nameGame) {
                return ['status' => 'Время матча в одной игре одно и то же, необходимо исправить.'];
            }
        }

        $game = GameLottery::where('name', $nameGame)->first();
        $match = new MatchLottery();
        $match->start_date = $start_date;
        $match->start_time = $start_time;

        $match->game_id = $game->id;
        $match->save();
        $match->game;

        return $match;
    }

    public function finishinghMatch(string|int $matchId, bool $isFinish)
    {
        $match = MatchLottery::where('id', $matchId)->first();
        if (is_null($match)) {
            return ['status' => 'Матч не найден.'];
        }

        if ($match->is_finished) {
            return ['status' => 'Матч уже завершен, результаты объявлены ранее.'];
        }

        $match->is_finished = $isFinish;
        $match->save();
        $match->game;

        //--- определение победителя и подсчет числа очков победителю
        event(new FinishMatchEvent($match));

        return $match;
    }

    /**
     * поля: user_id, match_id
     * проверки: Один пользователь не может дважды записаться на один и тот же матч.
     */
    public function saveUserGame(string|int $user_id, string|int $match_id)
    {
        $match = MatchLottery::where('id', $match_id)->first();
        if (is_null($match)) {
            return ['status' => 'Такого матча не существует.'];
        }

        //--- запись пользователя на матч
        $user = User::where('id', $user_id)->first();
        event(new SaveUserMatchEvent($user, $match, $match_id));

        return $user;
    }

    public function getMatches(string|int $gameId)
    {
        return GameLottery::where('id', $gameId)->first()->matches;
    }
}
