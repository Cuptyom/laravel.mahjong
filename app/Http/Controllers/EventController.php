<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
// Страница рейтинга
    public function rating($eventId)
    {
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
        
        return view('event.rating', compact('event', 'ratingList'));
    }
    
    public function description($eventId)
    {
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        return view('event.description', compact('event'));
    }
    
    public function rules($eventId)
    {
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        return view('event.rules', compact('event'));
    }
    public function games(Request $request, $event_id)
{
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
    ]);
    }
}