<?php

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameLottery extends Model
{
    use HasFactory, UsesUuid;

    public $timestamps = false;

    /**
     * Таблица БД, ассоциированная с моделью.
     *
     * @var string
     */
    protected $table = 'lottery_games';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name',];

    public function matches()
    {
        return $this->hasMany(MatchLottery::class, 'game_id')->orderBy('start_time');
    }
}
