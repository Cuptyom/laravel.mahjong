<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_type')->default('tournament');
            $table->string('event_name')->unique();
            $table->text('event_description')->nullable();
            $table->boolean('event_global_visability')->default(true);
            $table->boolean('rating_table_visability')->default(true);
            $table->integer('min_games')->default(0);
            $table->integer('start_score')->default(1500);
            $table->integer('uma_1st')->default(10000);
            $table->integer('uma_2nd')->default(5000);
            $table->integer('uma_3rd')->default(-5000);
            $table->integer('uma_4th')->default(-10000);
            $table->boolean('isEnd')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}