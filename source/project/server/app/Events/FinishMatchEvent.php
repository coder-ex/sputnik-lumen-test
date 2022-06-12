<?php

namespace App\Events;

use App\Models\MatchLottery;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class FinishMatchEvent extends Event
{
    use InteractsWithSockets; //, SerializesModels;

    /**
     * матч игры
     * @var \App\Models\MatchLottery $match
     */
    public $match;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\MatchLottery  $match
     * @return void
     */
    public function __construct(MatchLottery $match)
    {
        $this->match = $match;
    }
}
