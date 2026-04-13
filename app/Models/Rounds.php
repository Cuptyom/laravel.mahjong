<?php

namespace App\Models;  // было App

use Illuminate\Database\Eloquent\Model;

class Rounds extends Model
{
    protected $table = 'rounds';
    protected $primaryKey = 'round_id';
    protected $fillable = ['game_id', 'serian_number', 'round_name', 'round_end_type', 'renchan_count'];
    
    public function game()
    {
        return $this->belongsTo(Games::class, 'game_id', 'game_id');
    }
    
    public function roundResults()
    {
        return $this->hasMany(RoundResults::class, 'round_id', 'round_id');
    }
}