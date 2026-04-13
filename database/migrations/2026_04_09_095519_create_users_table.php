<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_login')->unique();
            $table->string('user_pass');
            $table->string('user_name');
            $table->string('user_country')->nullable();
            $table->string('user_city')->nullable();
            $table->string('user_phone')->unique()->nullable();
            $table->string('user_gmail')->unique()->nullable();
            $table->string('user_avatar')->nullable();
            $table->string('user_status')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}