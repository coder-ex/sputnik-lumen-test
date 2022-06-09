<?php

namespace Database\Seeders;

use App\Models\GameLottery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 10 users using the user factory
        GameLottery::factory()->count(10)->create();
    }
}
