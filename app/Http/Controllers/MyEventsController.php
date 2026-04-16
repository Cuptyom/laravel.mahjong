<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyEventsController extends Controller
{
    public function index(Request $request)
    {
        // Получаем ID пользователя из cookie
        $userId = $request->cookie('user_id');
        
        // Проверяем, авторизован ли пользователь
        if (!$userId) {
            return view('my_events.my_events', [
                'events' => collect(),
                'isAuthenticated' => false
            ]);
        }
        
        // Получаем ID событий, в которых участвует пользователь
        $eventIds = DB::table('event_players')
            ->where('user_id', $userId)
            ->pluck('event_id')
            ->toArray();
        
        if (empty($eventIds)) {
            $events = collect();
        } else {
            // Получаем события с пагинацией (без created_at)
            $events = DB::table('events')
                ->whereIn('event_id', $eventIds)
                ->orderBy('event_id', 'desc')  // ← сортировка по event_id
                ->paginate(10);
        }
        
        return view('my_events.my_events', [
            'events' => $events,
            'isAuthenticated' => true
        ]);
    }
}