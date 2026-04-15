<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundsTable extends Migration
{
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id('round_id');
            $table->foreignId('game_id')->constrained('games', 'game_id')->onDelete('cascade');
            $table->integer('serian_number');
            $table->string('round_name')->nullable(); // East 1, East 2, South 1 и т.д.
            $table->string('round_end_type')->nullable(); // ron, tsumo, ryuukyoku
            $table->integer('renchan_count')->default(0); // количество повторений (honba)
        });
    }

    public function down()
    {
        Schema::dropIfExists('rounds');
    }
}