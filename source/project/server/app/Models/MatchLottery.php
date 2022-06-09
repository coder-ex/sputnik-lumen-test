<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchLottery extends Model
{
    use HasFactory;

    public $timestamps = false;

    // const CREATED_AT = null;
    // const UPDATED_AT = 'start_time';

    /**
     * Таблица БД, ассоциированная с моделью.
     *
     * @var string
     */
    protected $table = 'lottery_game_matches';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    //protected $fillable = ['name',];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'game_id', 'winner_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'lottery_game_match_users', 'lottery_game_match_id', 'user_id');
    }

    public function game()
    {
        return $this->belongsTo(GameLottery::class);
    }
}
