<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class EventsSeeder extends Seeder
{
    public function run()
    {
        $existingCount = DB::table('events')->count();
        
        if ($existingCount >= 100) {
            echo "Events table already has {$existingCount} records. Skipping...\n";
            return;
        }
        
        $needed = 100 - $existingCount;
        echo "Adding {$needed} new events...\n";
        
        for($i = 0; $i < $needed; $i++){
            DB::table('events')->insertOrIgnore([
                'event_type' => Arr::random(['tournament', 'local', 'online']),
                'event_name' => Str::random(12),
                'event_description' => Str::random(100),
                'event_global_visability' => rand(0,1),
                'rating_table_visability' => rand(0,1),
                'min_games' => rand(0,50),
                'start_rating' => rand(25,35) * 1000,
                'uma_1st' => rand(-10, 10) * 1000,
                'uma_2nd' => rand(-10, 10) * 1000,
                'uma_3rd' => rand(-10, 10) * 1000,
                'uma_4th' => rand(-10, 10) * 1000,
                'isEnd' => rand(0,1),
            ]);
        }
    }
}