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
        DB::table('users')->insert([
                'user_login' => 'admin',
                'user_pass' => '1234',
                'user_name' => 'admin',
                'user_country' => '',
                'user_city' => '',
                'user_phone' => null,
                'user_gmail' =>'test@gmail.com',
                'user_avatar' => null,
                'user_status' => null,
            ]);
        DB::table('users')->insert([
                'user_login' => 'blocked',
                'user_pass' => '1234',
                'user_name' => 'blocked',
                'user_country' => '',
                'user_city' => '',
                'user_phone' => null,
                'user_gmail' =>'blocked@gmail.com',
                'user_avatar' => null,
                'user_status' => 'Blocked',
            ]);
        $needed = 20 - $existingCount;
        echo "Adding {$needed} new users...\n";
        
        for($i = 0; $i < $needed; $i++){
            DB::table('users')->insertOrIgnore([
                'user_login' => Str::random(10),
                'user_pass' => Str::random(10),
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