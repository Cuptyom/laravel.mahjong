<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GamesSeeder extends Seeder
{
    public function run()
    {
        $eventIds = DB::table('events')->pluck('event_id')->toArray();
        
        if (empty($eventIds)) {
            echo "No events found. Run EventsSeeder first.\n";
            return;
        }
        
        $targetCount = count($eventIds) * rand(2, 10);
        $existingCount = DB::table('games')->count();
        
        if ($existingCount >= $targetCount) {
            echo "Games table already has {$existingCount} records. Skipping...\n";
            return;
        }
        
        $needed = $targetCount - $existingCount;
        echo "Adding {$needed} games...\n";
        
        for($i = 0; $i < $needed; $i++){
            DB::table('games')->insertOrIgnore([
                'event_id' => $eventIds[array_rand($eventIds)],
                'game_date' => date('Y-m-d', strtotime('-' . rand(0, 60) . ' days')),
            ]);
        }
    }
}