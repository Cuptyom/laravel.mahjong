<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentGamesTable extends Migration
{
    public function up()
    {
        Schema::create('current_games', function (Blueprint $table) {
            $table->id('cur_game_id');
            $table->foreignId('event_id')->constrained('events', 'event_id')->onDelete('cascade');
            $table->timestamp('game_date')->useCurrent();
            // При необходимости можно добавить статус игры (active, finished) но пока не требуется
        });
    }

    public function down()
    {
        Schema::dropIfExists('current_games');
    }
}