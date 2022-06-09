<?php

namespace App\Listeners;

use App\Events\FinishMatchEvent;

class FinishMatchListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\FinishMatchEvent $event
     * @return void
     */
    public function handle(FinishMatchEvent $event)
    {
        $users = $event->match->users;
        $rand = mt_rand(0, count($users) - 1);
        $event->match->winner_id = $users[$rand]->id;
        $users[$rand]->points += $event->match->game->reward_points;
        $event->match->save();

        $users[$rand]->save();
    }
}
