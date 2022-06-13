<?php

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory, UsesUuid;

    //public $timestamps = false;
    // const CREATED_AT = 'start_date';
    // const UPDATED_AT = 'start_time';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password', 'activation_link'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function matches()
    {
        return $this->belongsToMany(MatchLottery::class, 'lottery_game_match_users', 'user_id', 'lottery_game_match_id');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}
