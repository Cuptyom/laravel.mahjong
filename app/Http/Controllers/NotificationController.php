<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // Показать все уведомления со статусом pending
    public function index()
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему, чтобы просмотреть уведомления');
        }
        
        $notifications = DB::table('event_invitation_notifications')
            ->join('events', 'event_invitation_notifications.event_id', '=', 'events.event_id')
            ->where('event_invitation_notifications.user_id', $userId)
            ->where('event_invitation_notifications.status', 'pending')
            ->select(
                'event_invitation_notifications.*',
                'events.event_name'
            )
            ->orderBy('event_invitation_notifications.notification_id', 'desc')
            ->get();
        
        return view('my_notifications.my_notifications', compact('notifications'));
    }
    
    // Принять приглашение
    public function accept($notificationId)
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        // Получаем уведомление
        $notification = DB::table('event_invitation_notifications')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();
        
        if (!$notification) {
            return redirect()->route('notifications.index')
                ->with('error', 'Уведомление не найдено или уже обработано');
        }
        
        // Обновляем статус уведомления
        DB::table('event_invitation_notifications')
            ->where('notification_id', $notificationId)
            ->update(['status' => 'accepted']);
        
        // Добавляем пользователя в event_players, если ещё не добавлен
        $exists = DB::table('event_players')
            ->where('event_id', $notification->event_id)
            ->where('user_id', $userId)
            ->exists();
        
        if (!$exists) {
            DB::table('event_players')->insert([
                'event_id' => $notification->event_id,
                'user_id' => $userId,
                'status' => 'player',
            ]);
        }
        
        return redirect()->route('event.rating', $notification->event_id)
            ->with('success', 'Вы присоединились к событию!');
    }
    
    // Отклонить приглашение
    public function reject($notificationId)
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $notification = DB::table('event_invitation_notifications')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();
        
        if (!$notification) {
            return redirect()->route('notifications.index')
                ->with('error', 'Уведомление не найдено или уже обработано');
        }
        
        DB::table('event_invitation_notifications')
            ->where('notification_id', $notificationId)
            ->update(['status' => 'rejected']);
        
        return redirect()->route('notifications.index')
            ->with('success', 'Приглашение отклонено');
    }

    // Получить количество непрочитанных уведомлений
    public static function getUnreadCount()
    {
        $userId = request()->cookie('user_id');
        if (!$userId) {
            return 0;
        }
        
        $count = DB::table('event_invitation_notifications')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->count();
        
        return $count;
    }
}