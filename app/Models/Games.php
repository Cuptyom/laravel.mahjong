<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    protected $table = 'games';
    protected $primaryKey = 'game_id';
    protected $fillable = ['event_id', 'game_date'];
    
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }
    
    public function gamePlayers()
    {
        return $this->hasMany(GamePlayers::class, 'game_id', 'game_id');
    }
    
    public function rounds()
    {
        return $this->hasMany(Rounds::class, 'game_id', 'game_id');
    }
}