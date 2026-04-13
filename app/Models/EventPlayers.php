<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPlayers extends Model
{
    protected $table = 'event_players';
    protected $fillable = ['event_id', 'user_id', 'status'];
    
    // Связи
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }
    
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}