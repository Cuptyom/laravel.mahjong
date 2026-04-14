<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = DB::table('events')
            ->where('event_global_visability', 1)
            ->where('isEnd', 0);
        
        // поиск
        if ($search) {
            $query->where('event_name', 'like', '%' . $search . '%');
        }
        
        // Пагинация
        $events = $query->paginate(10);
        
        return view('index', [
            'events' => $events,
            'search' => $search
        ]);
    }
}