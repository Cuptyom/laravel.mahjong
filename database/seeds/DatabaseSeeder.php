<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "Starting database seeding...\n\n";
        
        $this->call([
            UsersSeeder::class,
            EventsSeeder::class,
            EventPlayersSeeder::class,
            GamesSeeder::class,
            GamePlayersSeeder::class,
            RoundsSeeder::class,
            RoundResultsSeeder::class,
            EventInvitationNotificationSeeders::class,
        ]);
        
        echo "\nDatabase seeding completed!\n";
    }
}