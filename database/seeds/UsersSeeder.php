<?php
use Illuminate\Database\Seeder;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 
class UsersSeeder extends Seeder
{
    public function run(){
        //$user = ['user_login' => 'user1', 'user_pass' => 'user1', 'user_name' => 'Артём Шемелин', 'user_country' => 'Россия', 'user_city' => 'Череповец', 'user_phone' => '88005553535', 'user_gmail' => 'user1@gmail.com', 'user_avatar' => '', 'user_status' => ''];
        for($i = 0; $i<10; $i++){
            DB::table('Users')->insert(
                ['user_login' => Str::random(10),
                'user_pass' => Str::random(10),
                'user_name' => Str::random(10).' '.Str::random(10),
                'user_country' => 'Россия',
                'user_city' => Str::random(10),
                'user_phone' => Str::random(11,'0123456789'),
                'user_gmail' => Str::random(10).'@gmail.com',
                'user_avatar' => '',
                'user_status' => '']
            );
        }
    }
}
