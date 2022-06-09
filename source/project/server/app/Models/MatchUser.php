<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MatchUser extends Pivot
{
    /**
     * Указывает, что идентификаторы модели являются автоинкрементными
     *
     * @var bool
     */
    public $incrementing = true;

    protected $table = 'lottery_game_match_users';
}
