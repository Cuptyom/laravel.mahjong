<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GamePlayersSeeder extends Seeder
{
    public function run()
    {
        $gameIds = DB::table('games')->pluck('game_id')->toArray();
        $userIds = DB::table('users')->pluck('user_id')->toArray();
        $positions = ['East', 'South', 'West', 'North'];
        
        if (empty($gameIds) || empty($userIds)) {
            echo "No games or users found. Run GamesSeeder and UsersSeeder first.\n";
            return;
        }
        
        $targetCount = count($gameIds) * 4;
        $existingCount = DB::table('game_players')->count();
        
        if ($existingCount >= $targetCount) {
            echo "Game_players table already has {$existingCount} records. Skipping...\n";
            return;
        }
        
        $inserted = 0;
        
        foreach ($gameIds as $gameId) {
            $currentPlayers = DB::table('game_players')
                ->where('game_id', $gameId)
                ->pluck('user_id')
                ->toArray();
            
            $neededForGame = 4 - count($currentPlayers);
            
            if ($neededForGame <= 0) {
                continue;
            }
            
            $availableUsers = array_diff($userIds, $currentPlayers);
            shuffle($availableUsers);
            $selectedUsers = array_slice($availableUsers, 0, $neededForGame);
            
            foreach ($selectedUsers as $index => $userId) {
                DB::table('game_players')->insertOrIgnore([
                    'game_id' => $gameId,
                    'user_id' => $userId,
                    'start_position' => $positions[$index] ?? $positions[array_rand($positions)],
                ]);
                $inserted++;
            }
        }
        
        echo "Inserted {$inserted} game-player relations.\n";
    }
}