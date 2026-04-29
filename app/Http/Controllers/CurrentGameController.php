<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrentGameController extends Controller
{
    public function createForm()
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $events = DB::table('events')
            ->join('event_players', 'events.event_id', '=', 'event_players.event_id')
            ->where('event_players.user_id', $userId)
            ->where('events.isEnd', 0)
            ->select('events.*')
            ->get();
        
        return view('current_game.create', compact('events'));
    }
    
    public function getParticipants($eventId)
    {
        $participants = DB::table('event_players')
            ->join('users', 'event_players.user_id', '=', 'users.user_id')
            ->where('event_players.event_id', $eventId)
            ->select('users.user_id', 'users.user_name', 'users.user_login')
            ->get();
        
        $activePlayers = DB::table('current_games')
            ->join('current_rounds', 'current_games.cur_game_id', '=', 'current_rounds.cur_game_id')
            ->join('current_round_results', 'current_rounds.cur_round_id', '=', 'current_round_results.cur_round_id')
            ->where('current_games.event_id', $eventId)
            ->pluck('current_round_results.user_id')
            ->toArray();
        
        foreach ($participants as $participant) {
            $participant->is_active = in_array($participant->user_id, $activePlayers);
        }
        
        return response()->json($participants);
    }
    
    public function store(Request $request)
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'Войдите в систему'], 401);
        }
        
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'player_east' => 'required|exists:users,user_id',
            'player_south' => 'required|exists:users,user_id',
            'player_west' => 'required|exists:users,user_id',
            'player_north' => 'required|exists:users,user_id',
        ]);
        
        $eventId = $request->input('event_id');
        $players = [
            'East' => $request->input('player_east'),
            'South' => $request->input('player_south'),
            'West' => $request->input('player_west'),
            'North' => $request->input('player_north'),
        ];
        
        if (count(array_unique($players)) !== 4) {
            return response()->json(['error' => 'Все игроки должны быть разными']);
        }
        
        $activePlayers = DB::table('current_games')
            ->join('current_rounds', 'current_games.cur_game_id', '=', 'current_rounds.cur_game_id')
            ->join('current_round_results', 'current_rounds.cur_round_id', '=', 'current_round_results.cur_round_id')
            ->where('current_games.event_id', $eventId)
            ->pluck('current_round_results.user_id')
            ->toArray();
        
        foreach ($players as $position => $playerId) {
            if (in_array($playerId, $activePlayers)) {
                $playerName = DB::table('users')->where('user_id', $playerId)->value('user_name');
                return response()->json(['error' => "Игрок {$playerName} уже участвует в активной игре"]);
            }
        }
        
        $event = DB::table('events')->where('event_id', $eventId)->first();
        $startScore = $event->start_score;
        
        $curGameId = DB::table('current_games')->insertGetId([
            'event_id' => $eventId,
            'game_date' => now(),
        ]);
        
        $curRoundId = DB::table('current_rounds')->insertGetId([
            'cur_game_id' => $curGameId,
            'serial_number' => 0,
            'round_name' => '0',
            'round_end_type' => '',
            'renchan_count' => 0,
        ]);
        
        foreach ($players as $position => $playerId) {
            DB::table('current_round_results')->insert([
                'cur_round_id' => $curRoundId,
                'user_id' => $playerId,
                'start_position' => $position,
                'riichi_bet' => 0,
                'dead_hand' => 0,
                'chombo' => 0,
                'tempai' => 0,
                'points_sum' => $startScore,
                'points_change' => 0,
            ]);
        }
        
        return response()->json(['redirect' => route('current_game.show', $eventId)]);
    }
    
    public function show($eventId)
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $isParticipant = DB::table('event_players')
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
        
        if (!$isParticipant) {
            return view('current_game.error', [
                'message' => 'Вы не участвуете в этом событии',
                'event_id' => $eventId
            ]);
        }
        
        $currentGame = DB::table('current_games')
            ->where('event_id', $eventId)
            ->first();
        
        if (!$currentGame) {
            return view('current_game.error', [
                'message' => 'У вас сейчас нет активной игры в этом событии',
                'event_id' => $eventId
            ]);
        }
        
        $currentRound = DB::table('current_rounds')
            ->where('cur_game_id', $currentGame->cur_game_id)
            ->orderBy('serial_number', 'desc')
            ->first();
        
        if (!$currentRound) {
            return view('current_game.error', [
                'message' => 'Ошибка: раунды не найдены',
                'event_id' => $eventId
            ]);
        }
        
        $players = DB::table('current_round_results')
            ->join('users', 'current_round_results.user_id', '=', 'users.user_id')
            ->where('cur_round_id', $currentRound->cur_round_id)
            ->select(
                'current_round_results.*',
                'users.user_name',
                'users.user_login'
            )
            ->get();
        
        $myPosition = $players->where('user_id', $userId)->first()->start_position ?? null;
        
        $positionOrder = ['East', 'South', 'West', 'North'];
        $sortedPlayers = [];
        foreach ($positionOrder as $pos) {
            $player = $players->where('start_position', $pos)->first();
            if ($player) {
                $sortedPlayers[] = $player;
            }
        }
        
        $event = DB::table('events')->where('event_id', $eventId)->first();
        
        return view('current_game.show', compact('event', 'sortedPlayers', 'myPosition', 'currentGame', 'currentRound'));
    }
    
    public function myCurrentGames()
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $currentGames = DB::table('current_games')
            ->whereExists(function($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('current_rounds')
                    ->join('current_round_results', 'current_rounds.cur_round_id', '=', 'current_round_results.cur_round_id')
                    ->whereColumn('current_rounds.cur_game_id', 'current_games.cur_game_id')
                    ->where('current_round_results.user_id', $userId);
            })
            ->join('events', 'current_games.event_id', '=', 'events.event_id')
            ->select(
                'current_games.cur_game_id',
                'current_games.event_id',
                'current_games.game_date',
                'events.event_name',
                'events.event_type'
            )
            ->distinct()
            ->get();
        
        foreach ($currentGames as $game) {
            $lastRound = DB::table('current_rounds')
                ->where('cur_game_id', $game->cur_game_id)
                ->orderBy('serial_number', 'desc')
                ->first();
            
            if ($lastRound) {
                $players = DB::table('current_round_results')
                    ->join('users', 'current_round_results.user_id', '=', 'users.user_id')
                    ->where('cur_round_id', $lastRound->cur_round_id)
                    ->select('users.user_name', 'current_round_results.start_position', 'current_round_results.points_sum')
                    ->get();
                
                $game->players = $players;
            } else {
                $game->players = collect();
            }
        }
        
        return view('current_game.my_games', compact('currentGames'));
    }
    
    public function addRound(Request $request, $eventId)
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'Войдите в систему'], 401);
        }
        
        $roundEndType = $request->input('round_end_type');
        
        $currentGame = DB::table('current_games')
            ->where('event_id', $eventId)
            ->first();
        
        if (!$currentGame) {
            return response()->json(['error' => 'Активная игра не найдена']);
        }
        
        $currentRound = DB::table('current_rounds')
            ->where('cur_game_id', $currentGame->cur_game_id)
            ->orderBy('serial_number', 'desc')
            ->first();
        
        if (!$currentRound) {
            return response()->json(['error' => 'Раунды не найдены']);
        }
        
        $currentScores = DB::table('current_round_results')
            ->where('cur_round_id', $currentRound->cur_round_id)
            ->get()
            ->keyBy('user_id');
        
        $playerPositions = [];
        foreach ($currentScores as $score) {
            $playerPositions[$score->user_id] = $score->start_position;
        }
        
        $pointsChanges = [];
        
        switch ($roundEndType) {
            case 'ron':
                $pointsChanges = $this->calculateRonPoints(
                    $request->input('winner_id'),
                    $request->input('loser_id'),
                    $request->input('yaku', []),
                    $request->input('is_open') ? true : false,
                    (int)$request->input('fu', 30),
                    (int)$request->input('dora', 0),
                    $playerPositions,
                    $currentScores
                );
                break;
            case 'tsumo':
                $pointsChanges = $this->calculateTsumoPoints(
                    $request->input('winner_id'),
                    $request->input('yaku', []),
                    $request->input('is_open') ? true : false,
                    (int)$request->input('fu', 30),
                    (int)$request->input('dora', 0),
                    $playerPositions,
                    $currentScores
                );
                break;
            case 'draw':
                $pointsChanges = $this->calculateDrawPoints(
                    $request->input('tempai_players', []),
                    $playerPositions,
                    $currentScores
                );
                break;
            case 'nagasi-mangan':
                $pointsChanges = $this->calculateTsumoPoints(
                    $request->input('nagashi_winner'),
                    ['nagashi'],
                    false,
                    0,
                    0,
                    $playerPositions,
                    $currentScores,
                    5
                );
                break;
            case 'abortive-draw':
                $pointsChanges = $this->calculateChomboPoints(
                    $request->input('chombo_player'),
                    $playerPositions,
                    $currentScores
                );
                break;
            default:
                return response()->json(['error' => 'Неизвестный тип завершения']);
        }
        
        if (!$pointsChanges) {
            return response()->json(['error' => 'Ошибка расчёта очков']);
        }
        
        foreach ($pointsChanges as $playerId => $change) {
            $oldScore = $currentScores[$playerId]->points_sum;
            DB::table('current_round_results')
                ->where('cur_round_id', $currentRound->cur_round_id)
                ->where('user_id', $playerId)
                ->update([
                    'points_sum' => $oldScore + $change,
                    'points_change' => $change,
                    'riichi_bet' => 0,
                    'tempai' => 0,
                    'chombo' => 0,
                ]);
        }
        
        $newSerial = $currentRound->serial_number + 1;
        
        $newRoundId = DB::table('current_rounds')->insertGetId([
            'cur_game_id' => $currentGame->cur_game_id,
            'serial_number' => $newSerial,
            'round_name' => $this->getRoundName($newSerial, $playerPositions),
            'round_end_type' => $roundEndType,
            'renchan_count' => 0,
        ]);
        
        $updatedScores = DB::table('current_round_results')
            ->where('cur_round_id', $currentRound->cur_round_id)
            ->get()
            ->keyBy('user_id');
        
        foreach ($updatedScores as $playerId => $score) {
            DB::table('current_round_results')->insert([
                'cur_round_id' => $newRoundId,
                'user_id' => $playerId,
                'start_position' => $score->start_position,
                'riichi_bet' => 0,
                'dead_hand' => 0,
                'chombo' => 0,
                'tempai' => 0,
                'points_sum' => $score->points_sum,
                'points_change' => 0,
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    private function calculateHan($yakuList, $isOpen)
    {
        $yakuValues = [
            'tanyao' => 1, 'riichi' => 1, 'ippatsu' => 1, 'tsumo' => 1, 'pinfu' => 1,
            'iipeikou' => 1, 'yakuhai1' => 1, 'yakuhai2' => 2, 'yakuhai3' => 3,
            'yakuhai4' => 4, 'toitoi' => 2,
            'honitsu' => $isOpen ? 2 : 3,
            'chinitsu' => $isOpen ? 5 : 6,
            'chanta' => $isOpen ? 1 : 2,
            'junchan' => $isOpen ? 2 : 3,
        ];
        
        $totalHan = 0;
        foreach ($yakuList as $yaku) {
            if (isset($yakuValues[$yaku])) {
                $totalHan += $yakuValues[$yaku];
            }
        }
        return $totalHan;
    }
    
    private function calculateBasePoints($han, $fu)
    {
        if ($han >= 13) return 32000;
        if ($han >= 11) return 24000;
        if ($han >= 8) return 16000;
        if ($han >= 6) return 12000;
        if ($han >= 5) return 8000;
        return $fu * pow(2, 2 + $han);
    }
    
    private function roundUpToHundred($value)
    {
        return ceil($value / 100) * 100;
    }
    
    private function calculateRonPoints($winnerId, $loserId, $yakuList, $isOpen, $fu, $dora, $playerPositions, $currentScores)
    {
        $han = $this->calculateHan($yakuList, $isOpen) + $dora;
        $basePoints = $this->calculateBasePoints($han, $fu);
        $basePoints = $this->roundUpToHundred($basePoints);
        
        $isWinnerEast = ($playerPositions[$winnerId] == 'East');
        $payment = $isWinnerEast ? $basePoints * 6 / 4 : $basePoints;
        $payment = $this->roundUpToHundred($payment);
        
        $changes = [];
        $changes[$winnerId] = $payment;
        $changes[$loserId] = -$payment;
        return $changes;
    }
    
    private function calculateTsumoPoints($winnerId, $yakuList, $isOpen, $fu, $dora, $playerPositions, $currentScores, $customHan = null)
    {
        if ($customHan !== null) {
            $han = $customHan;
        } else {
            $han = $this->calculateHan($yakuList, $isOpen) + $dora;
        }
        
        $basePoints = $this->calculateBasePoints($han, $fu);
        $basePoints = $this->roundUpToHundred($basePoints);
        
        $isWinnerEast = ($playerPositions[$winnerId] == 'East');
        $changes = [];
        $changes[$winnerId] = 0;
        
        if ($isWinnerEast) {
            $payment = $basePoints;
            foreach ($currentScores as $playerId => $score) {
                if ($playerId == $winnerId) continue;
                $changes[$playerId] = -$payment;
                $changes[$winnerId] += $payment;
            }
        } else {
            $eastPayment = $this->roundUpToHundred(round(($basePoints * 2) / 4));
            $otherPayment = $this->roundUpToHundred(round($basePoints / 4));
            
            foreach ($currentScores as $playerId => $score) {
                if ($playerId == $winnerId) continue;
                if ($playerPositions[$playerId] == 'East') {
                    $changes[$playerId] = -$eastPayment;
                    $changes[$winnerId] += $eastPayment;
                } else {
                    $changes[$playerId] = -$otherPayment;
                    $changes[$winnerId] += $otherPayment;
                }
            }
        }
        
        $changes[$winnerId] = $this->roundUpToHundred($changes[$winnerId]);
        return $changes;
    }
    
    private function calculateDrawPoints($tempaiPlayers, $playerPositions, $currentScores)
    {
        $tempaiCount = count($tempaiPlayers);
        $changes = [];
        foreach ($currentScores as $playerId => $score) {
            $changes[$playerId] = 0;
        }
        
        if ($tempaiCount == 1) {
            $winnerId = $tempaiPlayers[0];
            $changes[$winnerId] = 3000;
            foreach ($currentScores as $playerId => $score) {
                if ($playerId != $winnerId) {
                    $changes[$playerId] = -1000;
                }
            }
        } elseif ($tempaiCount == 2) {
            foreach ($tempaiPlayers as $playerId) {
                $changes[$playerId] = 1500;
            }
            foreach ($currentScores as $playerId => $score) {
                if (!in_array($playerId, $tempaiPlayers)) {
                    $changes[$playerId] = -1500;
                }
            }
        } elseif ($tempaiCount == 3) {
            $notTempai = null;
            foreach ($currentScores as $playerId => $score) {
                if (!in_array($playerId, $tempaiPlayers)) {
                    $notTempai = $playerId;
                    break;
                }
            }
            if ($notTempai) {
                $changes[$notTempai] = -3000;
                foreach ($tempaiPlayers as $playerId) {
                    $changes[$playerId] = 1000;
                }
            }
        }
        return $changes;
    }
    
    private function calculateChomboPoints($chomboPlayerId, $playerPositions, $currentScores)
    {
        $changes = [];
        foreach ($currentScores as $playerId => $score) {
            $changes[$playerId] = 0;
        }
        
        $isChomboEast = ($playerPositions[$chomboPlayerId] == 'East');
        
        if ($isChomboEast) {
            $changes[$chomboPlayerId] = -12000;
            foreach ($currentScores as $playerId => $score) {
                if ($playerId != $chomboPlayerId) {
                    $changes[$playerId] = 4000;
                }
            }
        } else {
            $changes[$chomboPlayerId] = -8000;
            foreach ($currentScores as $playerId => $score) {
                if ($playerId == $chomboPlayerId) continue;
                if ($playerPositions[$playerId] == 'East') {
                    $changes[$playerId] = 4000;
                } else {
                    $changes[$playerId] = 2000;
                }
            }
        }
        return $changes;
    }
    
    private function getRoundName($serialNumber, $playerPositions)
    {
        $windCycle = ['East', 'South', 'West', 'North'];
        $windIndex = floor(($serialNumber - 1) / 4);
        $roundNumber = (($serialNumber - 1) % 4) + 1;
        
        if ($windIndex < 4) {
            return $windCycle[$windIndex] . ' ' . $roundNumber;
        }
        return 'Extra ' . ($serialNumber - 15);
    }
    public function finishGame($eventId)
{
    try {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'Войдите в систему'], 401);
        }
        
        // Получаем текущую игру
        $currentGame = DB::table('current_games')
            ->where('event_id', $eventId)
            ->first();
        
        if (!$currentGame) {
            return response()->json(['error' => 'Активная игра не найдена']);
        }
        
        // Проверяем, что пользователь является участником этой игры
        $isParticipant = DB::table('current_round_results')
            ->join('current_rounds', 'current_round_results.cur_round_id', '=', 'current_rounds.cur_round_id')
            ->where('current_rounds.cur_game_id', $currentGame->cur_game_id)
            ->where('current_round_results.user_id', $userId)
            ->exists();
        
        if (!$isParticipant) {
            return response()->json(['error' => 'Вы не являетесь участником этой игры'], 403);
        }
        
        // Получаем все раунды этой игры (кроме нулевого)
        $rounds = DB::table('current_rounds')
            ->where('cur_game_id', $currentGame->cur_game_id)
            ->where('serial_number', '>', 0)
            ->orderBy('serial_number', 'asc')
            ->get();
        
        // Если нет ни одного завершённого раунда, не даём завершить игру
        if ($rounds->isEmpty()) {
            return response()->json(['error' => 'Нельзя завершить игру без сыгранных раундов']);
        }
        
        // Получаем всех участников игры (позиции берём из первого раунда)
        $firstRound = $rounds->first();
        
        $playersInfo = DB::table('current_round_results')
            ->where('cur_round_id', $firstRound->cur_round_id)
            ->select('user_id', 'start_position')
            ->get()
            ->keyBy('user_id');
        
        // Создаём основную запись в таблице games
        $gameId = DB::table('games')->insertGetId([
            'event_id' => $eventId,
            'game_date' => $currentGame->game_date,
        ]);
        
        // Создаём записи в game_players (участники игры)
        foreach ($playersInfo as $playerId => $player) {
            DB::table('game_players')->insert([
                'game_id' => $gameId,
                'user_id' => $playerId,
                'start_position' => $player->start_position,
            ]);
        }
        
        // Для каждого раунда создаём запись в таблице rounds и переносим результаты
        foreach ($rounds as $round) {
            $newRoundId = DB::table('rounds')->insertGetId([
                'game_id' => $gameId,
                'serian_number' => $round->serial_number,
                'round_name' => $round->round_name,
                'round_end_type' => $round->round_end_type,
                'renchan_count' => $round->renchan_count,
            ]);
            
            $roundResults = DB::table('current_round_results')
                ->where('cur_round_id', $round->cur_round_id)
                ->get();
            
            foreach ($roundResults as $result) {
                DB::table('round_results')->insert([
                    'round_id' => $newRoundId,
                    'user_id' => $result->user_id,
                    'riichi_bet' => $result->riichi_bet,
                    'dead_hand' => $result->dead_hand,
                    'chombo' => $result->chombo,
                    'tempai' => $result->tempai,
                    'points_sum' => $result->points_sum,
                    'points_change' => $result->points_change,
                ]);
            }
        }
        
        // Удаляем временные данные
        $allRounds = DB::table('current_rounds')
            ->where('cur_game_id', $currentGame->cur_game_id)
            ->get();
        
        foreach ($allRounds as $round) {
            DB::table('current_round_results')
                ->where('cur_round_id', $round->cur_round_id)
                ->delete();
        }
        
        DB::table('current_rounds')
            ->where('cur_game_id', $currentGame->cur_game_id)
            ->delete();
        
        DB::table('current_games')
            ->where('cur_game_id', $currentGame->cur_game_id)
            ->delete();
        
        return response()->json(['success' => true, 'game_id' => $gameId]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Ошибка: ' . $e->getMessage()], 500);
    }
}
public function deleteRound($eventId, $roundId)
{
    $userId = request()->cookie('user_id');
    
    if (!$userId) {
        return response()->json(['error' => 'Войдите в систему'], 401);
    }
    
    // Получаем игру
    $currentGame = DB::table('current_games')
        ->where('event_id', $eventId)
        ->first();
    
    if (!$currentGame) {
        return response()->json(['error' => 'Активная игра не найдена']);
    }
    
    // Проверяем, что пользователь является участником
    $isParticipant = DB::table('current_round_results')
        ->join('current_rounds', 'current_round_results.cur_round_id', '=', 'current_rounds.cur_round_id')
        ->where('current_rounds.cur_game_id', $currentGame->cur_game_id)
        ->where('current_round_results.user_id', $userId)
        ->exists();
    
    if (!$isParticipant) {
        return response()->json(['error' => 'Вы не являетесь участником этой игры'], 403);
    }
    
    // Получаем раунд для удаления
    $roundToDelete = DB::table('current_rounds')
        ->where('cur_round_id', $roundId)
        ->where('cur_game_id', $currentGame->cur_game_id)
        ->first();
    
    if (!$roundToDelete) {
        return response()->json(['error' => 'Раунд не найден']);
    }
    
    // Удаляем все раунды с этим serial_number и выше
    DB::table('current_rounds')
        ->where('cur_game_id', $currentGame->cur_game_id)
        ->where('serial_number', '>=', $roundToDelete->serial_number)
        ->delete();
    
    // Удаляем соответствующие результаты (через join или подзапрос)
    DB::table('current_round_results')
        ->whereIn('cur_round_id', function($query) use ($currentGame, $roundToDelete) {
            $query->select('cur_round_id')
                ->from('current_rounds')
                ->where('cur_game_id', $currentGame->cur_game_id)
                ->where('serial_number', '>=', $roundToDelete->serial_number);
        })
        ->delete();
    
    return response()->json(['success' => true]);
}
}