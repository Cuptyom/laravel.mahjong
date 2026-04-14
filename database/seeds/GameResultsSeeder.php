<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameResultsSeeder extends Seeder
{
    public function run()
    {
        $gameIds = DB::table('games')->pluck('game_id')->toArray();
        
        if (empty($gameIds)) {
            echo "No games found. Skipping GameResultsSeeder...\n";
            return;
        }
        
        // Для каждой игры создаём 4 результата (по числу игроков)
        $inserted = 0;
        
        foreach ($gameIds as $gameId) {
            // Получаем игроков, участвовавших в этой игре
            $players = DB::table('game_players')
                ->where('game_id', $gameId)
                ->pluck('user_id')
                ->toArray();
            
            // Если игроков меньше 4, создаём сколько есть
            foreach ($players as $userId) {
                // Проверяем, нет ли уже результата для этой пары
                $exists = DB::table('game_results')
                    ->where('game_id', $gameId)
                    ->where('user_id', $userId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('game_results')->insert([
                        'game_id' => $gameId,
                        'user_id' => $userId,
                        'end_score' => rand(10000, 50000),
                        'rating_change' => rand(-10000, 10000),
                    ]);
                    $inserted++;
                }
            }
        }
        
        echo "Inserted {$inserted} game results.\n";
    }
}