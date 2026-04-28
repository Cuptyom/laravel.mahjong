<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentRound extends Model
{
    protected $table = 'current_rounds';
    protected $primaryKey = 'cur_round_id';
    // Если используем timestamps, они есть в миграции, но можно отключить если не нужны:
    // public $timestamps = false;

    protected $fillable = [
        'cur_game_id',
        'serial_number',
        'round_name',
        'round_end_type',
        'renchan_count',
    ];

    public function currentGame()
    {
        return $this->belongsTo(CurrentGame::class, 'cur_game_id', 'cur_game_id');
    }

    public function currentRoundResults()
    {
        return $this->hasMany(CurrentRoundResult::class, 'cur_round_id', 'cur_round_id');
    }
}