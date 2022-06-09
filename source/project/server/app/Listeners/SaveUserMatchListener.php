<?php

namespace App\Listeners;

use App\Events\SaveUserMatchEvent;

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
            print_r(json_encode(['status' => 'На матч может записаться не больше [' . $count . '] в игре количества участников.'], JSON_UNESCAPED_UNICODE));
            $flag = true;
        }

        if (!$flag) {
            foreach ($event->user->matches as $match) {
                if ($match->id === $event->match_id) {
                    print_r(json_encode(['status' => 'Пользователь уже записан на данный матч.'], JSON_UNESCAPED_UNICODE));
                    $flag = true;
                    break;
                }
            }
        }


        if (!$flag)
            $event->user->matches()->attach($event->match_id);
    }
}
