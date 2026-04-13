<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'Events';
    protected $fillable = ['event_id', 'event_type',  'event_name',  'event_description',   'event_global_visability', 'rating_table_visability', 'min_games',   'start_rating',    'uma_1st', 'uma_2nd', 'uma_3rd', 'uma_4th', 'isEnd'];
}