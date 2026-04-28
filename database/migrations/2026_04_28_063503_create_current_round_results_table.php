<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentRoundResultsTable extends Migration
{
    public function up()
    {
        Schema::create('current_round_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cur_round_id')->constrained('current_rounds', 'cur_round_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->boolean('riichi_bet')->default(false);
            $table->boolean('dead_hand')->default(false);
            $table->boolean('chombo')->default(false);
            $table->boolean('tempai')->default(false);
            $table->integer('points_sum')->default(0);
            $table->integer('points_change')->default(0);
            $table->string('start_position')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('current_round_results');
    }
}