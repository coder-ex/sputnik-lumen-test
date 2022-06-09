<?php

namespace Database\Factories;

use App\Models\MatchLottery;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchLotteryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MatchLottery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'start_date' => date('Y-m-d H:i:s'),
            'start_time' => date('Y-m-d H:i:s'),
            'game_id' => $this->faker->unique(true)->numberBetween(1, 10),
            'winner_id' => $this->faker->unique(true)->numberBetween(1, 5),
        ];
    }
}
