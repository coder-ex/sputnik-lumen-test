<?php

namespace App\Events;

use App\Models\MatchLottery;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class SaveUserMatchEvent extends Event
{
    use InteractsWithSockets, SerializesModels;

    /**
     * пользователь
     *
     * @var \App\Models\User $user
     */
    public $user;

    /**
     * матч игры
     * @var \App\Models\MatchLottery $match
     */
    public $match;

    /**
     * id матча игры
     * @var int $match_id
     */
    public $match_id;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User          $user
     * @param \App\Models\MatchLottery  $match
     * @param int                       $match_id
     * @return void
     */
    public function __construct(User $user, MatchLottery $match, int $match_id)
    {
        $this->user  = $user;
        $this->match = $match;
        $this->match_id = $match_id;
    }
}
