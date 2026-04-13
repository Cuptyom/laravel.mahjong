<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoundResultsSeeder extends Seeder
{
    public function run()
    {
        $roundIds = DB::table('rounds')->pluck('round_id')->toArray();
        
        if (empty($roundIds)) {
            echo "No rounds found. Run RoundsSeeder first.\n";
            return;
        }
        
        echo "Adding round results...\n";
        
        $inserted = 0;
        
        foreach ($roundIds as $roundId) {
            $gameId = DB::table('rounds')->where('round_id', $roundId)->value('game_id');
            $players = DB::table('game_players')
                ->where('game_id', $gameId)
                ->pluck('user_id')
                ->toArray();
            
            $existingResults = DB::table('round_results')
                ->where('round_id', $roundId)
                ->pluck('user_id')
                ->toArray();
            
            $availablePlayers = array_diff($players, $existingResults);
            
            foreach ($availablePlayers as $userId) {
                DB::table('round_results')->insertOrIgnore([
                    'round_id' => $roundId,
                    'user_id' => $userId,
                    'riichi_bet' => rand(0, 1),
                    'dead_hand' => rand(0, 1),
                    'chombo' => rand(0, 1),
                    'tempai' => rand(0, 1),
                    'points_sum' => rand(-5000, 12000),
                    'points_change' => rand(-100, 100),
                ]);
                $inserted++;
            }
        }
        
        echo "Inserted {$inserted} round results.\n";
    }
}