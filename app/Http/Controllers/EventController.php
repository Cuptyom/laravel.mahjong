<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function rating($eventId)
    {
        $userId = request()->cookie('user_id');
        $userRole = null;
        if ($userId) {
            $userEvent = DB::table('event_players')
                ->where('event_id', $eventId)
                ->where('user_id', $userId)
                ->first();
            if ($userEvent) {
                $userRole = $userEvent->status;
            }
        }
        $isAdmin = ($userRole === 'admin');
        // Получаем информацию о событии
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        // Получаем текущего пользователя
        $userId = request()->cookie('user_id');
        $user = null;
        if ($userId) {
            $user = DB::table('users')->where('user_id', $userId)->first();
        }
        
        // Проверяем доступ к рейтингу
        $canViewRating = true;
        
        // Если рейтинг скрыт от всех
        if ($event->rating_table_visability == 0) {
            // Если пользователь не авторизован
            if (!$user) {
                $canViewRating = false;
            } else {
                // Проверяем, участвует ли пользователь в событии (есть ли запись в event_players)
                $isParticipant = DB::table('event_players')
                    ->where('event_id', $eventId)
                    ->where('user_id', $userId)
                    ->exists(); // <-- Убрана проверка на статус 'approved'
                
                if (!$isParticipant) {
                    $canViewRating = false;
                }
            }
        }
        
        if (!$canViewRating) {
            return view('event.rating_denied', compact('event'));
        }
         
        // Получаем всех игроков и сумму их rating_change в этом событии
        $players = DB::table('game_results')
            ->join('games', 'game_results.game_id', '=', 'games.game_id')
            ->join('users', 'game_results.user_id', '=', 'users.user_id')
            ->where('games.event_id', $eventId)
            ->select(
                'users.user_id',
                'users.user_name',
                'users.user_avatar',
                'game_results.rating_change'
            )
            ->get();
        
        // Формируем рейтинг: ТОЛЬКО сумма rating_change (без start_rating)
        $ratings = [];
        $gamesCount = [];
        
        foreach ($players as $player) {
            $playerUserId = $player->user_id;
            
            if (!isset($ratings[$playerUserId])) {
                $ratings[$playerUserId] = 0;  // ← теперь начинаем с 0
                $gamesCount[$playerUserId] = 0;
            }
            
            $ratings[$playerUserId] += $player->rating_change;
            $gamesCount[$playerUserId]++;
        }
        
        // Преобразуем в массив для сортировки
        $ratingList = [];
        foreach ($ratings as $playerUserId => $rating) {
            $playerData = DB::table('users')->where('user_id', $playerUserId)->first();
            $ratingList[] = (object)[
                'user_id' => $playerUserId,
                'user_name' => $playerData->user_name,
                'user_avatar' => $playerData->user_avatar ?? 'default.jpg',
                'rating' => $rating,
                'games_played' => $gamesCount[$playerUserId]
            ];
        }
        
        // Сортируем по убыванию рейтинга
        usort($ratingList, function($a, $b) {
            return $b->rating <=> $a->rating;
        });
        
        return view('event.rating', compact('event', 'ratingList', 'isAdmin'));
    }
    
    public function description($eventId)
    {
        $userId = request()->cookie('user_id');
        $userRole = null;
        if ($userId) {
            $userEvent = DB::table('event_players')
                ->where('event_id', $eventId)
                ->where('user_id', $userId)
                ->first();
            if ($userEvent) {
                $userRole = $userEvent->status;
            }
        }
        $isAdmin = ($userRole === 'admin');
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        return view('event.description', compact('event', 'isAdmin'));
    }
    
    public function rules($eventId)
{
    $userId = request()->cookie('user_id');
    $userRole = null;
    if ($userId) {
        $userEvent = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();
        if ($userEvent) {
            $userRole = $userEvent->status;
        }
    }
    $isAdmin = ($userRole === 'admin');
    
    $event = DB::table('events')->where('event_id', $eventId)->first(); // ← одна строка
    
    if (!$event) {
        abort(404, 'Событие не найдено');
    }
    
    return view('event.rules', compact('event', 'isAdmin'));
}
    public function games(Request $request, $event_id)
    {
        $userId = request()->cookie('user_id');
        $userRole = null;
        if ($userId) {
            $userEvent = DB::table('event_players')
                ->where('event_id', $event_id)  // ← было $eventId, исправлено на $event_id
                ->where('user_id', $userId)
                ->first();
            if ($userEvent) {
                $userRole = $userEvent->status;
            }
        }
        $isAdmin = ($userRole === 'admin');
        
        // Проверяем, существует ли событие
        $event = DB::table('events')->where('event_id', $event_id)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        // Получаем все игры этого события с пагинацией
        $games = DB::table('games')
            ->where('event_id', $event_id)
            ->orderBy('game_date', 'desc')
            ->orderBy('game_id', 'desc')
            ->paginate(10);
        
        // Для каждой игры собираем раунды
        foreach ($games as $game) {
            $game->rounds = DB::table('rounds')
                ->where('game_id', $game->game_id)
                ->orderBy('serian_number', 'asc')
                ->get();
            
            // Для каждого раунда собираем результаты
            foreach ($game->rounds as $round) {
                $round->results = DB::table('round_results')
                    ->where('round_id', $round->round_id)
                    ->get();
                
                // Добавляем информацию об игроках
                foreach ($round->results as $result) {
                    $player = DB::table('users')
                        ->where('user_id', $result->user_id)
                        ->first();
                    $result->user_name = $player->user_name ?? 'Неизвестный';
                    $result->user_login = $player->user_login ?? '';
                }
            }
        }
        
        return view('event.games', [
            'event' => $event,
            'games' => $games,
            'isAdmin' => $isAdmin,  // ← добавили передачу isAdmin
        ]);
    }
    // Страница редактирования события
public function edit($eventId)
{
    $event = DB::table('events')->where('event_id', $eventId)->first();
    
    if (!$event) {
        abort(404, 'Событие не найдено');
    }
    
    // Проверяем права (только admin)
    $userId = request()->cookie('user_id');
    $userRole = null;
    if ($userId) {
        $userEvent = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();
        if ($userEvent) {
            $userRole = $userEvent->status;
        }
    }
    
    if ($userRole !== 'admin') {
        abort(403, 'У вас нет прав для редактирования этого события');
    }
    
    $isAdmin = true;
    
    return view('event.edit', compact('event', 'isAdmin'));
}

// Обработка обновления события
public function update(Request $request, $eventId)
{
    $event = DB::table('events')->where('event_id', $eventId)->first();
    
    if (!$event) {
        abort(404, 'Событие не найдено');
    }
    
    // Проверяем права
    $userId = request()->cookie('user_id');
    $userRole = null;
    if ($userId) {
        $userEvent = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();
        if ($userEvent) {
            $userRole = $userEvent->status;
        }
    }
    
    if ($userRole !== 'admin') {
        abort(403, 'У вас нет прав для редактирования этого события');
    }
    
    // Валидация
    $request->validate([
        'event_name' => 'required|string|max:255',
        'event_description' => 'nullable|string',
        'start_score' => 'required|integer',
        'uma_1st' => 'required|integer',
        'uma_2nd' => 'required|integer',
        'uma_3rd' => 'required|integer',
        'uma_4th' => 'required|integer',
    ]);
    
    // Обновление
    DB::table('events')
        ->where('event_id', $eventId)
        ->update([
            'event_name' => $request->input('event_name'),
            'event_description' => $request->input('event_description'),
            'start_score' => $request->input('start_score'),
            'uma_1st' => $request->input('uma_1st'),
            'uma_2nd' => $request->input('uma_2nd'),
            'uma_3rd' => $request->input('uma_3rd'),
            'uma_4th' => $request->input('uma_4th'),
        ]);
    
    return redirect()->route('event.rating', $eventId)
        ->with('success', 'Событие успешно обновлено');
}
}