<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameResultsTable extends Migration
{
    public function up()
    {
        Schema::create('game_results', function (Blueprint $table) {
            $table->id('game_result_id');
            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('end_score');
            $table->integer('rating_change');
            
            // Внешние ключи
            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            // Уникальная пара: один игрок не может иметь два результата в одной игре
            $table->unique(['game_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_results');
    }
}