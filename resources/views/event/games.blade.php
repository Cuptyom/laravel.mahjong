@extends('layout')

@section('title', 'История игр - ' . $event->event_name)

@section('content')
<div class="container-fluid px-0">
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin, 'isParticipant' => $isParticipant])
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">История игр</h5>
                </div>
                <div class="card-body p-0">
                    @if($games->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($games as $game)
                                <div class="list-group-item">
                                    <br>
                                    <br>
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h2 class="mb-1 text-primary">Игра #{{ $game->game_id }}</h2>
                                            <p class="mb-1 text-muted small">{{ date('d.m.Y', strtotime($game->game_date)) }}</p>
                                            <p class="mb-2 small">
                                                <strong>Раундов:</strong> {{ $game->rounds->count() }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            @if($canDeleteGame)
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteGameModal"
                                                        data-game-id="{{ $game->game_id }}"
                                                        data-game-date="{{ date('d.m.Y H:i', strtotime($game->game_date)) }}">
                                                    Отменить игру
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Детали раундов-->
                                    @if($game->rounds->count() > 0)
                                        <div class="mt-3 pt-2 border-top">
                                            @foreach($game->rounds as $round)
                                                <div class="round-item py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                    <!-- Заголовок раунда -->
                                                    <div class="fw-bold mb-2">
                                                        {{ $round->round_name }}
                                                        @if($round->round_end_type == 'abortive-draw')
                                                            <span class="badge bg-danger ms-2">Перераздача</span>
                                                        @elseif($round->round_end_type == 'draw')
                                                            <span class="badge bg-secondary ms-2">Ничья</span>
                                                        @elseif($round->round_end_type == 'nagasi-mangan')
                                                            <span class="badge bg-warning ms-2">Нагаши Манган</span>
                                                        @elseif($round->round_end_type == 'ron')
                                                            <span class="badge bg-success ms-2">Рон</span>
                                                        @elseif($round->round_end_type == 'tsumo')
                                                            <span class="badge bg-info ms-2">Цумо</span>
                                                        @endif
                                                        @if($round->renchan_count > 0)
                                                            <span class="badge bg-secondary ms-1">ренчан x{{ $round->renchan_count }}</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Результаты всех игроков -->
                                                    @if($round->round_end_type == 'abortive-draw')
                                                        @php $chomboPlayer = $round->results->where('chombo', 1)->first(); @endphp
                                                        <div class="alert py-2 mb-0">
                                                            <strong>Чомбо.</strong> Перераздача из-за ошибки игрока 
                                                            <strong>{{ $chomboPlayer->user_name ?? 'Неизвестный' }}</strong>
                                                            @if($chomboPlayer && $chomboPlayer->riichi_bet)
                                                                (была ставка ричи)
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Игрок</th>
                                                                        <th class="text-center">Ставка ричи</th>
                                                                        <th class="text-center">Нотен / Темпай</th>
                                                                        <th class="text-center">Изменение очков</th>
                                                                        <th class="text-center">Итоговые очки</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($round->results as $result)
                                                                        <tr>
                                                                            <td>
                                                                                <strong>{{ $result->user_name }}</strong>
                                                                                <span class="text-muted ms-1">({{ $result->user_login }})</span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                @if($result->riichi_bet)
                                                                                    <span class="badge bg-danger">Да</span>
                                                                                @else
                                                                                    <span class="badge bg-secondary">Нет</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                @if($round->round_end_type == 'draw' || $round->round_end_type == 'nagasi-mangan')
                                                                                    @if($result->tempai)
                                                                                        <span class="badge bg-success">Темпай</span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">Нотен</span>
                                                                                    @endif
                                                                                @else
                                                                                    —
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                @if($result->points_change > 0)
                                                                                    <span class="text-success fw-bold">+{{ number_format($result->points_change) }}</span>
                                                                                @elseif($result->points_change < 0)
                                                                                    <span class="text-danger fw-bold">{{ number_format($result->points_change) }}</span>
                                                                                @else
                                                                                    <span class="text-muted">0</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <strong>{{ number_format($result->points_sum) }}</strong>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="p-3">
                            {{ $games->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            В этом событии пока нет сыгранных игр.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal подтверждения удаления -->
<div class="modal fade" id="deleteGameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите отменить эту игру?</p>
                <p><strong>Игра #<span id="deleteGameId"></span></strong></p>
                <p class="text-danger">Внимание! Это действие удалит:</p>
                <ul>
                    <li>Результаты всех раундов</li>
                    <li>Все раунды игры</li>
                    <li>Участников игры</li>
                    <li>Саму игру</li>
                </ul>
                <p class="text-danger">Отменить это действие будет невозможно!</p>
            </div>
            <div class="modal-footer">
                <form id="deleteGameForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger">Да, отменить игру</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Скрипт для модального окна удаления
    const deleteGameModal = document.getElementById('deleteGameModal');
    if (deleteGameModal) {
        deleteGameModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const gameId = button.getAttribute('data-game-id');
            document.getElementById('deleteGameId').textContent = gameId;
            const form = document.getElementById('deleteGameForm');
            form.action = `/event/{{ $event->event_id }}/game/${gameId}/delete`;
        });
    }
</script>

<style>
    .round-item:last-child {
        border-bottom: none !important;
    }
</style>
@endsection