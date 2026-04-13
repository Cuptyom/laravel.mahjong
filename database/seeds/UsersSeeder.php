<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $existingCount = DB::table('users')->count();
        
        if ($existingCount >= 20) {
            echo "Users table already has {$existingCount} records. Skipping...\n";
            return;
        }
        
        $needed = 20 - $existingCount;
        echo "Adding {$needed} new users...\n";
        
        for($i = 0; $i < $needed; $i++){
            DB::table('users')->insertOrIgnore([
                'user_login' => Str::random(10),
                'user_pass' => bcrypt('password123'),
                'user_name' => Str::random(8) . ' ' . Str::random(8),
                'user_country' => Arr::random(['Россия', 'США', 'Япония', 'Китай', 'Германия', 'Франция']),
                'user_city' => Str::random(8),
                'user_phone' => '7' . rand(9000000000, 9999999999),
                'user_gmail' => Str::random(8) . '@gmail.com',
                'user_avatar' => null,
                'user_status' => null,
            ]);
        }
    }
}