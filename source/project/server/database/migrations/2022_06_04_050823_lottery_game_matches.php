<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_game_matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('start_date', $precision = 0);
            $table->timestamp('start_time', $precision = 0);
            $table->boolean('is_finished')->default(0);

            $table->uuid('game_id')->nullable();
            $table->foreign('game_id')->references('id')->on('lottery_games')->onDelete('cascade');

            $table->uuid('winner_id')->nullable();
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lottery_game_matches');
    }
};
