<?php

namespace Database\Seeders;

use App\Models\MatchLottery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MatchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 10 users using the user factory
        MatchLottery::factory()->count(10)->create();
    }
}
