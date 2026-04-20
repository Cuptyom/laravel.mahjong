<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateEventController extends Controller
{
    public function showForm()
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return view('create_event.create_event', [
                'isAuthenticated' => false
            ]);
        }
        
        return view('create_event.create_event', [
            'isAuthenticated' => true
        ]);
    }
    
    public function store(Request $request)
{
    $userId = request()->cookie('user_id');
    
    if (!$userId) {
        return redirect('/login')->with('error', 'Войдите в систему, чтобы создать событие');
    }
    
    $request->validate([
        'event_name' => 'required|string|max:255',
        'event_type' => 'required|in:tournament,local,online',
        'event_description' => 'nullable|string',
        'event_global_visability' => 'boolean',
        'rating_table_visability' => 'boolean',
        'min_games' => 'integer|min:0',
        'start_score' => 'required|integer',
        'uma_1st' => 'required|integer',
        'uma_2nd' => 'required|integer',
        'uma_3rd' => 'required|integer',
        'uma_4th' => 'required|integer',
    ]);
    
    // Проверяем, существует ли событие с таким названием
    $existingEvent = DB::table('events')
        ->where('event_name', $request->input('event_name'))
        ->first();
    
    if ($existingEvent) {
        return back()->with('error', 'Событие с таким названием уже существует')->withInput();
    }
    
    $eventId = DB::table('events')->insertGetId([
        'event_name' => $request->input('event_name'),
        'event_type' => $request->input('event_type'),
        'event_description' => $request->input('event_description'),
        'event_global_visability' => $request->input('event_global_visability', 1),
        'rating_table_visability' => $request->input('rating_table_visability', 1),
        'min_games' => $request->input('min_games', 0),
        'start_score' => $request->input('start_score'),
        'uma_1st' => $request->input('uma_1st'),
        'uma_2nd' => $request->input('uma_2nd'),
        'uma_3rd' => $request->input('uma_3rd'),
        'uma_4th' => $request->input('uma_4th'),
        'isEnd' => 0,
    ]);
    
    DB::table('event_players')->insert([
        'event_id' => $eventId,
        'user_id' => $userId,
        'status' => 'admin',
    ]);
    
    return redirect()->route('event.rating', $eventId)
        ->with('success', 'Событие успешно создано!');
}
}