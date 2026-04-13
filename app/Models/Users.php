<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'Users';
    protected $fillable = ['user_id', 'user_login', 'user_pass', 'user_name', 'user_country', 'user_city', 'user_phone', 'user_gmail', 'user_avatar', 'user_status'];
}