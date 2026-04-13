<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoundsSeeder extends Seeder
{
    public function run()
    {
        $gameIds = DB::table('games')->pluck('game_id')->toArray();
        $roundNames = ['East 1', 'East 2', 'East 3', 'East 4', 'South 1', 'South 2', 'South 3', 'South 4'];
        $endTypes = ['ron', 'tsumo', 'ryuukyoku'];
        
        if (empty($gameIds)) {
            echo "No games found. Run GamesSeeder first.\n";
            return;
        }
        
        $targetCount = count($gameIds) * rand(4, 8);
        $existingCount = DB::table('rounds')->count();
        
        if ($existingCount >= $targetCount) {
            echo "Rounds table already has {$existingCount} records. Skipping...\n";
            return;
        }
        
        $needed = $targetCount - $existingCount;
        echo "Adding {$needed} rounds...\n";
        
        for($i = 0; $i < $needed; $i++){
            DB::table('rounds')->insertOrIgnore([
                'game_id' => $gameIds[array_rand($gameIds)],
                'serian_number' => rand(1, 8),
                'round_name' => $roundNames[array_rand($roundNames)],
                'round_end_type' => $endTypes[array_rand($endTypes)],
                'renchan_count' => rand(0, 5),
            ]);
        }
    }
}