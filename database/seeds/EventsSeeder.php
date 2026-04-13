<?php
use Illuminate\Database\Seeder;
use App\Models\Events;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;  

class EventsSeeder extends Seeder
{
    public function run()
    {
        //$event = ['event_type' => 'tournament',  'event_name'=>'Event1', 'event_description' => 'тестовое описание', 'event_global_visability' => '1', 'rating_table_visability' => '1', 'min_games' => '10',   'start_rating' => '25000', 'uma_1st' => '10000', 'uma_2nd' => '5000', 'uma_3rd' => '-5000', 'uma_4th' => '-10000', 'isEnd' => '0'];
        for($i = 0; $i < 10; $i++){
            DB::table('Events')->insert(
                ['event_type' => Arr::random('tournament', 'local', 'online'),
                'event_name'=>Str::random(10),
                'event_description' => Str::random(100),
                'event_global_visability' => rand(0,1),
                'rating_table_visability' => rand(0,1),
                'min_games' => rand(0,50),
                'start_rating' => rand(25,35) * 1000,
                'uma_1st' => rand(-10, 10) * 1000,
                'uma_2nd' => rand(-10, 10) * 1000,
                'uma_3rd' => rand(-10, 10) * 1000,
                'uma_4th' => rand(-10, 10) * 1000,
                'isEnd' => rand(0,1)]);
        }
    }
}
