<?php

namespace App\Listeners;

use App\Events\SaveUserMatchEvent;
use Exception;

class SaveUserMatchListener
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
     * @param  \App\Events\SaveUserMatchEvent $event
     * @return void
     */
    public function handle(SaveUserMatchEvent $event)
    {
        $flag = false;
        $count = $event->match->game->gamer_count;
        if (count($event->match->users) >= $count) {
            throw new Exception('На матч может записаться не больше [' . $count . '] в игре количества участников.');
            $flag = true;
        }

        if (!$flag) {
            foreach ($event->user->matches as $match) {
                if ($match->id === $event->match_id) {
                    throw new Exception('Пользователь уже записан на данный матч.');
                    $flag = true;
                    break;
                }
            }
        }


        if (!$flag)
            $event->user->matches()->attach($event->match_id);
    }
}
