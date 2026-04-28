<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentRoundsTable extends Migration
{
    public function up()
    {
        Schema::create('current_rounds', function (Blueprint $table) {
            $table->id('cur_round_id');
            $table->foreignId('cur_game_id')->constrained('current_games', 'cur_game_id')->onDelete('cascade');
            $table->integer('serial_number');
            $table->string('round_name'); // например "East 1", "South 2"
            $table->string('round_end_type')->nullable(); // ron, tsumo, draw, nagasi-mangan, abortive-draw
            $table->integer('renchan_count')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('current_rounds');
    }
}