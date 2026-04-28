<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentGame extends Model
{
    protected $table = 'current_games';
    protected $primaryKey = 'cur_game_id';
    public $timestamps = false; // если не используем created_at/updated_at, но в миграции их нет, можно отключить

    protected $fillable = [
        'event_id',
        'game_date',
    ];

    // Связи
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }

    public function currentRounds()
    {
        return $this->hasMany(CurrentRound::class, 'cur_game_id', 'cur_game_id');
    }
}