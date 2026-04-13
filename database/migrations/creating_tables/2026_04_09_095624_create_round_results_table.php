<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundResultsTable extends Migration
{
    public function up()
    {
        Schema::create('round_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained('rounds', 'round_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->boolean('riichi_bet')->default(false);
            $table->boolean('dead_hand')->default(false);
            $table->boolean('chombo')->default(false);
            $table->boolean('tempai')->default(false);
            $table->integer('points_sum')->default(0);     // очки, полученные в раунде
            $table->integer('points_change')->default(0);  // изменение рейтинга
            
            $table->unique(['round_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('round_results');
    }
}