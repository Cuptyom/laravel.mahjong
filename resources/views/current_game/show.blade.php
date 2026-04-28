@extends('layout')

@section('title', 'Игра - ' . $event->event_name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <!-- Игровой стол -->
            <div class="game-table">
                <!-- Север (верх) -->
                <div class="position north">
                    @php
                        $north = null;
                        foreach ($sortedPlayers as $player) {
                            if ($player->start_position == 'North') {
                                $north = $player;
                                break;
                            }
                        }
                    @endphp
                    @if($north)
                        <div class="player-card">
                            <div class="player-name">
                                <span class="direction-badge"> Север</span>
                                {{ $north->user_name }}
                            </div>
                            <div class="player-score">{{ number_format($north->points_sum) }}</div>
                            @if($north->riichi_bet)
                                <span class="riichi-badge">リーチ</span>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Запад (слева) и Восток (справа) -->
                <div class="middle-row">
                    <div class="position west">
                        @php
                            $west = null;
                            foreach ($sortedPlayers as $player) {
                                if ($player->start_position == 'West') {
                                    $west = $player;
                                    break;
                                }
                            }
                        @endphp
                        @if($west)
                            <div class="player-card">
                                <div class="player-name">
                                    <span class="direction-badge">Запад</span>
                                    {{ $west->user_name }}
                                </div>
                                <div class="player-score">{{ number_format($west->points_sum) }}</div>
                                @if($west->riichi_bet)
                                    <span class="riichi-badge">リーチ</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="position east">
                        @php
                            $east = null;
                            foreach ($sortedPlayers as $player) {
                                if ($player->start_position == 'East') {
                                    $east = $player;
                                    break;
                                }
                            }
                        @endphp
                        @if($east)
                            <div class="player-card">
                                <div class="player-name">
                                    <span class="direction-badge">Восток</span>
                                    {{ $east->user_name }}
                                </div>
                                <div class="player-score">{{ number_format($east->points_sum) }}</div>
                                @if($east->riichi_bet)
                                    <span class="riichi-badge">リーチ</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Юг -->
                <div class="position south">
                    @php
                        $south = null;
                        foreach ($sortedPlayers as $player) {
                            if ($player->start_position == 'South') {
                                $south = $player;
                                break;
                            }
                        }
                    @endphp
                    @if($south)
                        <div class="player-card">
                            <div class="player-name">
                                <span class="direction-badge">Юг</span>
                                {{ $south->user_name }}
                            </div>
                            <div class="player-score">{{ number_format($south->points_sum) }}</div>
                            @if($south->riichi_bet)
                                <span class="riichi-badge">リーチ</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Кнопки -->
            <div class="text-center mt-4">
                <button class="btn btn-primary" id="addRoundBtn" data-bs-toggle="modal" data-bs-target="#addRoundModal">
                    Добавить раунд
                </button>
                <button class="btn btn-success" id="finishGameBtn">Завершить игру</button>
            </div>
            
            <!-- История раундов -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">История раундов</h5>
                        </div>
                        <div class="card-body p-0">
                            @php
                                $pastRounds = DB::table('current_rounds')
                                    ->where('cur_game_id', $currentGame->cur_game_id)
                                    ->where('serial_number', '>', 0)
                                    ->orderBy('serial_number', 'asc')
                                    ->get();
                            @endphp
                            
                            @if($pastRounds->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>№</th>
                                                <th>Раунд</th>
                                                <th>Тип</th>
                                                <th colspan="4">Результаты</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pastRounds as $round)
                                                @php
                                                    $roundResults = DB::table('current_round_results')
                                                        ->join('users', 'current_round_results.user_id', '=', 'users.user_id')
                                                        ->where('cur_round_id', $round->cur_round_id)
                                                        ->select('current_round_results.*', 'users.user_name')
                                                        ->get();
                                                @endphp
                                                <tr class="table-secondary">
                                                    <td colspan="6" class="py-2">
                                                        <strong>Раунд {{ $round->serial_number }}: {{ $round->round_name }}</strong>
                                                        <span class="badge bg-info ms-2">{{ $round->round_end_type }}</span>
                                                        @if($round->renchan_count > 0)
                                                            <span class="badge bg-warning ms-1">ренчан x{{ $round->renchan_count }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    @foreach($roundResults as $result)
                                                        <td class="text-center" style="min-width: 120px;">
                                                            <strong>{{ $result->user_name }}</strong>
                                                            <div class="small text-muted">{{ $result->start_position }}</div>
                                                            @if($result->points_change != 0)
                                                                @if($result->points_change > 0)
                                                                    <span class="text-success fw-bold">+{{ number_format($result->points_change) }}</span>
                                                                @else
                                                                    <span class="text-danger fw-bold">{{ number_format($result->points_change) }}</span>
                                                                @endif
                                                                <div class="small">→ {{ number_format($result->points_sum) }}</div>
                                                            @else
                                                                <div class="text-muted">0</div>
                                                                <div class="small">{{ number_format($result->points_sum) }}</div>
                                                            @endif
                                                            @if($result->riichi_bet)
                                                                <span class="badge bg-danger d-block mt-1">リーチ</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    Пока нет завершённых раундов
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно -->
<div class="modal fade" id="addRoundModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление раунда</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRoundForm">
                    @csrf
                    <input type="hidden" name="cur_game_id" id="cur_game_id" value="{{ $currentGame->cur_game_id ?? '' }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Тип завершения</label>
                        <select name="round_end_type" id="roundEndType" class="form-select" required>
                            <option value="">-- Выберите тип --</option>
                            <option value="ron">Рон (победа на сбросе)</option>
                            <option value="tsumo">Цумо (победа на самовытягивании)</option>
                            <option value="draw">Ничья (рюкёку)</option>
                            <option value="nagasi-mangan">Нагаши манган</option>
                            <option value="abortive-draw">Абортивная ничья (перераздача)</option>
                        </select>
                    </div>
                    
                    <div id="formWin" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Победитель</label>
                                <select name="winner_id" id="winner" class="form-select">
                                    <option value="">-- Выберите --</option>
                                    @foreach($sortedPlayers as $player)
                                        <option value="{{ $player->user_id }}">{{ $player->user_name }} ({{ $player->start_position }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="loserDiv">
                                <label class="form-label">Проигравший (набросивший)</label>
                                <select name="loser_id" id="loser" class="form-select">
                                    <option value="">-- Выберите --</option>
                                    @foreach($sortedPlayers as $player)
                                        <option value="{{ $player->user_id }}">{{ $player->user_name }} ({{ $player->start_position }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_open" id="isOpen" class="form-check-input" value="1">
                                <label class="form-check-label">Открытая рука</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Победные комбинации (Яку)</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="tanyao" class="form-check-input">
                                        <label class="form-check-label">Таняо (1 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="riichi" class="form-check-input">
                                        <label class="form-check-label">Риичи (1 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="ippatsu" class="form-check-input">
                                        <label class="form-check-label">Иппацу (1 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="tsumo" class="form-check-input">
                                        <label class="form-check-label">Цумо (1 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="pinfu" class="form-check-input">
                                        <label class="form-check-label">Пинфу (1 хан)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="iipeikou" class="form-check-input">
                                        <label class="form-check-label">Иппейко (1 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="yakuhai1" class="form-check-input">
                                        <label class="form-check-label">Якухай 1 (1 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="yakuhai2" class="form-check-input">
                                        <label class="form-check-label">Якухай 2 (2 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="yakuhai3" class="form-check-input">
                                        <label class="form-check-label">Якухай 3 (3 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="yakuhai4" class="form-check-input">
                                        <label class="form-check-label">Якухай 4 (4 хан)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="honitsu" class="form-check-input">
                                        <label class="form-check-label">Хонницу (2/3 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="chinitsu" class="form-check-input">
                                        <label class="form-check-label">Чиницу (5/6 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="chanta" class="form-check-input">
                                        <label class="form-check-label">Чанта (1/2 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="junchan" class="form-check-input">
                                        <label class="form-check-label">Джунчан (2/3 хан)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="yaku[]" value="toitoi" class="form-check-input">
                                        <label class="form-check-label">Той-той (2 хан)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Количество фу</label>
                                <input type="number" name="fu" id="fu" class="form-control" value="30" min="20" step="10">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Количество дор (ханов)</label>
                                <input type="number" name="dora" id="dora" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div id="formDraw" style="display: none;">
                        <label class="form-label fw-bold mb-2">Темпай игроки</label>
                        <div class="mb-3">
                            @foreach($sortedPlayers as $player)
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="tempai_players[]" value="{{ $player->user_id }}" class="form-check-input">
                                    <label class="form-check-label">{{ $player->user_name }} ({{ $player->start_position }})</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info">
                            💡 Ничья: если 1 темпай — он получает 3000; если 2 — они получают по 1500; если 3 — нетемпай платит по 1000 каждому.
                        </div>
                    </div>
                    
                    <div id="formNagashi" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Победитель</label>
                            <select name="nagashi_winner" id="nagashiWinner" class="form-select">
                                <option value="">-- Выберите --</option>
                                @foreach($sortedPlayers as $player)
                                    <option value="{{ $player->user_id }}">{{ $player->user_name }} ({{ $player->start_position }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            💡 Нагаши манган считается как цумо с 5 ханами.
                        </div>
                    </div>
                    
                    <div id="formAbortive" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Совершивший ошибку (чомбо)</label>
                            <select name="chombo_player" id="chomboPlayer" class="form-select">
                                <option value="">-- Выберите --</option>
                                @foreach($sortedPlayers as $player)
                                    <option value="{{ $player->user_id }}">{{ $player->user_name }} ({{ $player->start_position }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            💡 Абортивная ничья: если виновный — восток, он платит каждому по 4000; иначе — востоку 4000, остальным по 2000.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveRoundBtn">Записать раунд</button>
            </div>
        </div>
    </div>
</div>

<script>
    const roundEndType = document.getElementById('roundEndType');
    const formWin = document.getElementById('formWin');
    const formDraw = document.getElementById('formDraw');
    const formNagashi = document.getElementById('formNagashi');
    const formAbortive = document.getElementById('formAbortive');
    const loserDiv = document.getElementById('loserDiv');
    
    roundEndType.addEventListener('change', function() {
        formWin.style.display = 'none';
        formDraw.style.display = 'none';
        formNagashi.style.display = 'none';
        formAbortive.style.display = 'none';
        
        switch(this.value) {
            case 'ron':
                formWin.style.display = 'block';
                loserDiv.style.display = 'block';
                break;
            case 'tsumo':
                formWin.style.display = 'block';
                loserDiv.style.display = 'none';
                break;
            case 'draw':
                formDraw.style.display = 'block';
                break;
            case 'nagasi-mangan':
                formNagashi.style.display = 'block';
                break;
            case 'abortive-draw':
                formAbortive.style.display = 'block';
                break;
        }
    });
    
    document.getElementById('saveRoundBtn').addEventListener('click', function() {
        const roundEndTypeValue = roundEndType.value;
        
        if (!roundEndTypeValue) {
            alert('Выберите тип завершения');
            return;
        }
        
        if (roundEndTypeValue === 'ron') {
            const winner = document.getElementById('winner').value;
            const loser = document.getElementById('loser').value;
            if (!winner || !loser) {
                alert('Выберите победителя и проигравшего');
                return;
            }
        }
        
        if (roundEndTypeValue === 'tsumo') {
            const winner = document.getElementById('winner').value;
            if (!winner) {
                alert('Выберите победителя');
                return;
            }
        }
        
        if (roundEndTypeValue === 'nagasi-mangan') {
            const winner = document.getElementById('nagashiWinner').value;
            if (!winner) {
                alert('Выберите победителя');
                return;
            }
        }
        
        if (roundEndTypeValue === 'abortive-draw') {
            const chombo = document.getElementById('chomboPlayer').value;
            if (!chombo) {
                alert('Выберите игрока, совершившего ошибку');
                return;
            }
        }
        
        const formData = new FormData(document.getElementById('addRoundForm'));
        const eventId = {{ $event->event_id }};
        
        const saveBtn = document.getElementById('saveRoundBtn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Сохранение...';
        
        fetch(`/current_game/${eventId}/add_round`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else if (data.error) {
                alert(data.error);
                saveBtn.disabled = false;
                saveBtn.textContent = 'Записать раунд';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при сохранении раунда');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Записать раунд';
        });
    });
</script>

<style>
    .game-table {
        background: #2d5a2c;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: relative;
        min-height: 500px;
    }
    
    .position {
        position: absolute;
    }
    
    .north {
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .south {
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .west {
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .east {
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .middle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 400px;
        padding: 0 80px;
    }
    
    .player-card {
        background: white;
        border-radius: 12px;
        padding: 12px 20px;
        min-width: 160px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    
    .player-name {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .direction-badge {
        display: inline-block;
        background: #e9ecef;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 10px;
        margin-right: 8px;
    }
    
    .player-score {
        font-size: 24px;
        font-weight: bold;
        color: #0d6efd;
    }
    
    .riichi-badge {
        display: inline-block;
        background: #dc3545;
        color: white;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 10px;
        margin-top: 5px;
    }
    
    @media (max-width: 768px) {
        .player-card {
            min-width: 120px;
            padding: 8px 12px;
        }
        .player-score {
            font-size: 18px;
        }
        .middle-row {
            padding: 0 20px;
        }
    }
</style>
@endsection