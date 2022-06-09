<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{

    public $timestamps = false;

    /**
     * Таблица БД, ассоциированная с моделью.
     *
     * @var string
     */
    protected $table = 'tokens';

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
