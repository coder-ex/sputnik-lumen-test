<?php

namespace Database\Factories;

use App\Models\GameLottery;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameLotteryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameLottery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Game ' . $this->faker->name,
        ];
    }
}
