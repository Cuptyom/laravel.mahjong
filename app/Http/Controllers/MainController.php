<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index(Request $request)
    {
        // Получаем поисковый запрос
        $search = $request->input('search');
        
        // Строим запрос
        $query = DB::table('events')
            ->where('event_global_visability', 1)
            ->where('isEnd', 0);
        
        // Если есть поисковый запрос, фильтруем по названию
        if ($search) {
            $query->where('event_name', 'like', '%' . $search . '%');
        }
        
        // Пагинация по 10 записей на страницу
        $events = $query->paginate(10);
        
        // Передаём данные в представление
        return view('index', [
            'events' => $events,
            'search' => $search
        ]);
    }
}