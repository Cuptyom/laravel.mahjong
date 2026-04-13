<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamePlayersTable extends Migration
{
    public function up()
    {
        Schema::create('game_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games', 'game_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->enum('start_position', ['East', 'South', 'West', 'North'])->nullable();
            
            $table->unique(['game_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_players');
    }
}