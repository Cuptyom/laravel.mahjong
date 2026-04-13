<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePlayers extends Model
{
    protected $table = 'game_players';
    protected $fillable = ['game_id', 'user_id', 'start_position'];
    
    public function game()
    {
        return $this->belongsTo(Games::class, 'game_id', 'game_id');
    }
    
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}