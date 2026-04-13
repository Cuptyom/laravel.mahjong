<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "Starting database seeding...\n\n";
        
        $this->call(UsersSeeder::class);
        $this->call(EventsSeeder::class);
        $this->call(EventPlayersSeeder::class);
        $this->call(GamesSeeder::class);
        $this->call(GamePlayersSeeder::class);
        $this->call(RoundsSeeder::class);
        $this->call(RoundResultsSeeder::class);
        $this->call(EventInvitationNotificationSeeders::class);
        
        echo "\nDatabase seeding completed!\n";
    }
}