<?php

namespace App\Models;  // было App\Model

use Illuminate\Database\Eloquent\Model;

class RoundResults extends Model
{
    protected $table = 'round_results';
    protected $fillable = ['round_id', 'user_id', 'riichi_bet', 'dead_hand', 'chombo', 'tempai', 'points_sum', 'points_change'];
    
    public function round()
    {
        return $this->belongsTo(Rounds::class, 'round_id', 'round_id');
    }
    
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}