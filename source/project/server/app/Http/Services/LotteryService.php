<?php

namespace App\Http\Services;

use App\Events\Event;
use App\Events\FinishMatchEvent;
use App\Events\SaveUserMatchEvent;
use App\Models\GameLottery;
use App\Models\MatchLottery;
use App\Models\User;
use DateTime;
use Exception;

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

    /**
     * создание матча игры
     *
     * @param string $date - дата начала
     * @param string $time - время начала
     * @param string $name - имя игры
     * @return MatchLottery
     */
    public function createMatch(string $date, string $time, string $name): MatchLottery
    {
        $dateCurrent = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' 00:00:00');
        $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00');
        if ($dateCurrent > $startDate) {
            throw new Exception('Дата начала старта матча не может быть раньше сегодня.');
        }

        $startTime = DateTime::createFromFormat('H:i:s', $time);
        $match_t = MatchLottery::where('start_time', $startTime)->first();
        if (!is_null($match_t)) {
            if ($match_t->start_time === $startTime->format('Y-m-d H:i:s') && $match_t->game->name === $name) {
                throw new Exception('Время матча в одной игре одно и то же, необходимо исправить.');
            }
        }

        $game = GameLottery::where('name', $name)->first();
        $match = new MatchLottery();
        $match->start_date = $startDate;
        $match->start_time = $startTime;

        $match->game_id = $game->id;
        $match->save();
        $match->game;

        return $match;
    }

    /**
     * завершение матча
     *
     * @param string|integer $matchId
     * @param boolean $isFinish
     * @return MatchLottery
     */
    public function finishinghMatch(string|int $matchId, bool $isFinish): MatchLottery
    {
        $match = MatchLottery::where('id', $matchId)->first();
        if (is_null($match)) {
            throw new Exception('Матч не найден.');
        }

        if ($match->is_finished) {
            throw new Exception('Матч уже завершен, результаты объявлены ранее.');
        }

        $match->is_finished = $isFinish;
        $match->save();
        $match->game;

        //--- определение победителя и подсчет числа очков победителю
        event(new FinishMatchEvent($match));

        return $match;
    }

    /**
     * запись пользователя на игру
     *
     * @param string|integer $userId
     * @param string|integer $matchId
     * @return User
     * @remark проверки: Один пользователь не может дважды записаться на один и тот же матч.
     */
    public function saveUserGame(string|int $userId, string|int $matchId): User
    {
        $match = MatchLottery::where('id', $matchId)->first();
        if (is_null($match)) {
            throw new Exception('Такого матча не существует.');
        }

        //--- запись пользователя на матч
        $user = User::where('id', $userId)->first();
        event(new SaveUserMatchEvent($user, $match, $matchId));

        return $user;
    }

    /**
     * получение всех матчей игры
     *
     * @param string|integer $gameId
     * @return GameLottery
     */
    public function getMatches(string|int $gameId): GameLottery
    {
        return GameLottery::where('id', $gameId)->first()->matches;
    }
}
