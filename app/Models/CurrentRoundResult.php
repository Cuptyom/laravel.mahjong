<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentRoundResult extends Model
{
    protected $table = 'current_round_results';
    // Если используем timestamps, они есть в миграции
    // public $timestamps = false;

    protected $fillable = [
        'cur_round_id',
        'user_id',
        'riichi_bet',
        'dead_hand',
        'chombo',
        'tempai',
        'points_sum',
        'points_change',
    ];

    public function currentRound()
    {
        return $this->belongsTo(CurrentRound::class, 'cur_round_id', 'cur_round_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}