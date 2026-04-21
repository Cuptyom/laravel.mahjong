<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventInvitationNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('event_invitation_notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events', 'event_id')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            
            $table->unique(['user_id', 'event_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_invitation_notifications');
    }
}