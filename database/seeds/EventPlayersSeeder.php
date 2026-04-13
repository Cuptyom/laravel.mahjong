<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventPlayersSeeder extends Seeder
{
    public function run()
    {
        $eventIds = DB::table('events')->pluck('event_id')->toArray();
        $userIds = DB::table('users')->pluck('user_id')->toArray();
        $statuses = ['pending', 'approved', 'rejected'];
        
        if (empty($eventIds) || empty($userIds)) {
            echo "No events or users found. Run UsersSeeder and EventsSeeder first.\n";
            return;
        }
        
        $targetCount = count($eventIds) * 3;
        $existingCount = DB::table('event_players')->count();
        
        if ($existingCount >= $targetCount) {
            echo "Event_players table already has {$existingCount} records. Skipping...\n";
            return;
        }
        
        $needed = $targetCount - $existingCount;
        echo "Adding {$needed} event-player relations...\n";
        
        $inserted = 0;
        $attempts = 0;
        $maxAttempts = $needed * 3;
        
        while ($inserted < $needed && $attempts < $maxAttempts) {
            $eventId = $eventIds[array_rand($eventIds)];
            $userId = $userIds[array_rand($userIds)];
            
            $exists = DB::table('event_players')
                ->where('event_id', $eventId)
                ->where('user_id', $userId)
                ->exists();
            
            if (!$exists) {
                DB::table('event_players')->insert([
                    'event_id' => $eventId,
                    'user_id' => $userId,
                    'status' => $statuses[array_rand($statuses)],
                ]);
                $inserted++;
            }
            $attempts++;
        }
        
        echo "Inserted {$inserted} event-player relations.\n";
    }
}