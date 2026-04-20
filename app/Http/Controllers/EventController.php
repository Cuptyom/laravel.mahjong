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
        
        // Проверяем, участвует ли пользователь в событии (для вкладки "Пригласить")
        $isParticipant = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();

        $event = DB::table('events')->where('event_id', $eventId)->first();
        if (!$event) {
            abort(404, 'Событие не найдено');
        }

        // Проверка доступа к рейтингу
        $user = null;
        if ($userId) {
            $user = DB::table('users')->where('user_id', $userId)->first();
        }
        $canViewRating = true;
        if ($event->rating_table_visability == 0) {
            if (!$user) {
                $canViewRating = false;
            } else {
                $isParticipantCheck = DB::table('event_players')
                    ->where('event_id', $eventId)
                    ->where('user_id', $userId)
                    ->exists();
                if (!$isParticipantCheck) {
                    $canViewRating = false;
                }
            }
        }
        if (!$canViewRating) {
            return view('event.rating_denied', compact('event'));
        }

        // 1. Получаем всех пользователей, которые либо есть в event_players,
        //    либо имеют игры в этом событии
        $participantsFromPlayers = DB::table('event_players')
            ->where('event_id', $eventId)
            ->pluck('user_id')
            ->toArray();

        $participantsFromGames = DB::table('games')
            ->join('game_results', 'games.game_id', '=', 'game_results.game_id')
            ->where('games.event_id', $eventId)
            ->pluck('game_results.user_id')
            ->unique()
            ->toArray();

        // Объединяем и убираем дубликаты
        $allParticipantIds = array_unique(array_merge($participantsFromPlayers, $participantsFromGames));

        if (empty($allParticipantIds)) {
            $ratingList = [];
            return view('event.rating', compact('event', 'ratingList', 'isAdmin', 'isParticipant'));
        }

        // 2. Получаем сумму rating_change и количество игр для всех этих пользователей
        $stats = DB::table('game_results')
            ->join('games', 'game_results.game_id', '=', 'games.game_id')
            ->where('games.event_id', $eventId)
            ->whereIn('game_results.user_id', $allParticipantIds)
            ->select(
                'game_results.user_id',
                DB::raw('SUM(game_results.rating_change) as total_rating'),
                DB::raw('COUNT(*) as games_played')
            )
            ->groupBy('game_results.user_id')
            ->get()
            ->keyBy('user_id');

        // 3. Формируем итоговый список
        $ratingList = [];
        foreach ($allParticipantIds as $participantId) {
            $playerData = DB::table('users')->where('user_id', $participantId)->first();
            if (!$playerData) continue;

            $totalRating = isset($stats[$participantId]) ? $stats[$participantId]->total_rating : 0;
            $gamesPlayed = isset($stats[$participantId]) ? $stats[$participantId]->games_played : 0;

            $ratingList[] = (object)[
                'user_id' => $participantId,
                'user_name' => $playerData->user_name,
                'user_avatar' => $playerData->user_avatar ?? 'default.jpg',
                'rating' => $totalRating,
                'games_played' => $gamesPlayed
            ];
        }

        // 4. Сортируем по убыванию рейтинга
        usort($ratingList, function($a, $b) {
            return $b->rating <=> $a->rating;
        });

        return view('event.rating', compact('event', 'ratingList', 'isAdmin', 'isParticipant'));
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
        
        // Проверяем, участвует ли пользователь в событии (для вкладки "Пригласить")
        $isParticipant = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
        
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        return view('event.description', compact('event', 'isAdmin', 'isParticipant'));
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
        
        // Проверяем, участвует ли пользователь в событии (для вкладки "Пригласить")
        $isParticipant = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
        
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        return view('event.rules', compact('event', 'isAdmin', 'isParticipant'));
    }
    
    public function games(Request $request, $event_id)
    {
        $userId = request()->cookie('user_id');
        $userRole = null;
        if ($userId) {
            $userEvent = DB::table('event_players')
                ->where('event_id', $event_id)
                ->where('user_id', $userId)
                ->first();
            if ($userEvent) {
                $userRole = $userEvent->status;
            }
        }
        $isAdmin = ($userRole === 'admin');
        
        // Проверяем, участвует ли пользователь в событии (для вкладки "Пригласить")
        $isParticipant = DB::table('event_players')
            ->where('event_id', $event_id)
            ->where('user_id', $userId)
            ->exists();
        
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
            'isAdmin' => $isAdmin,
            'isParticipant' => $isParticipant,
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
        
        // Для участника (админ тоже участник)
        $isParticipant = true;
        
        return view('event.edit', compact('event', 'isAdmin', 'isParticipant'));
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
    
    // Страница приглашения игроков
    // Страница приглашения игроков
public function invitePlayers($eventId)
{
    $event = DB::table('events')->where('event_id', $eventId)->first();
    if (!$event) {
        abort(404, 'Событие не найдено');
    }
    
    $userId = request()->cookie('user_id');
    if (!$userId) {
        return redirect('/login')->with('error', 'Войдите в систему');
    }
    
    // Проверяем, участвует ли пользователь в событии
    $isParticipant = DB::table('event_players')
        ->where('event_id', $eventId)
        ->where('user_id', $userId)
        ->exists();
    
    if (!$isParticipant) {
        abort(403, 'Вы не участвуете в этом событии');
    }
    
    $search = request()->input('search');
    $users = collect();
    
    if ($search) {
        // Разбиваем поисковый запрос на отдельные слова
        $searchTerms = explode(' ', trim($search));
        
        // Ищем пользователей по логину или имени (мягкий поиск)
        $query = DB::table('users')
            ->where('user_id', '!=', $userId); // исключаем самого себя
        
        foreach ($searchTerms as $term) {
            if (strlen($term) >= 1) {
                $query->where(function($q) use ($term) {
                    $q->where('user_login', 'like', '%' . $term . '%')
                      ->orWhere('user_name', 'like', '%' . $term . '%');
                });
            }
        }
        
        $users = $query->get();
        
        // Отмечаем, кто уже приглашён или уже участвует
        foreach ($users as $user) {
            // Проверяем, уже ли участвует
            $user->alreadyParticipant = DB::table('event_players')
                ->where('event_id', $eventId)
                ->where('user_id', $user->user_id)
                ->exists();
            
            // Проверяем, уже ли есть приглашение
            $user->alreadyInvited = DB::table('event_invitation_notifications')
                ->where('event_id', $eventId)
                ->where('user_id', $user->user_id)
                ->where('status', 'pending')
                ->exists();
        }
    }
    
    $isAdmin = DB::table('event_players')
        ->where('event_id', $eventId)
        ->where('user_id', $userId)
        ->value('status') === 'admin';
    
    return view('event.invite_players', compact('event', 'users', 'search', 'isAdmin', 'isParticipant'));
}

    // Отправка приглашения
    public function invitePlayerPost(Request $request, $eventId)
    {
        $userId = request()->cookie('user_id');
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $event = DB::table('events')->where('event_id', $eventId)->first();
        if (!$event) {
            abort(404, 'Событие не найдено');
        }
        
        // Проверяем, участвует ли отправитель
        $isParticipant = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
        
        if (!$isParticipant) {
            abort(403, 'Вы не участвуете в этом событии');
        }
        
        $targetUserId = $request->input('user_id');
        
        // Нельзя пригласить самого себя
        if ($targetUserId == $userId) {
            return back()->with('error', 'Нельзя пригласить самого себя');
        }
        
        // Проверяем, существует ли пользователь
        $targetUser = DB::table('users')->where('user_id', $targetUserId)->first();
        if (!$targetUser) {
            return back()->with('error', 'Пользователь не найден');
        }
        
        // Проверяем, не участвует ли уже
        $alreadyParticipant = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $targetUserId)
            ->exists();
        
        if ($alreadyParticipant) {
            return back()->with('error', 'Этот пользователь уже участвует в событии');
        }
        
        // Проверяем, нет ли уже приглашения
        $alreadyInvited = DB::table('event_invitation_notifications')
            ->where('event_id', $eventId)
            ->where('user_id', $targetUserId)
            ->where('status', 'pending')
            ->exists();
        
        if ($alreadyInvited) {
            return back()->with('error', 'Этот пользователь уже приглашён');
        }
        
        // Создаём приглашение
        DB::table('event_invitation_notifications')->insert([
            'event_id' => $eventId,
            'user_id' => $targetUserId,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return back()->with('success', 'Приглашение отправлено пользователю ' . $targetUser->user_name);
    }
}