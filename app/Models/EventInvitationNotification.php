<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventInvitationNotification extends Model
{
    protected $table = 'event_invitation_notifications';
    protected $primaryKey = 'notification_id';
    protected $fillable = ['user_id', 'event_id', 'status'];
    
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
    
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }
}