@extends('layout')

@section('title', 'История игр - ' . $event->event_name)

@section('content')
<div class="container py-4">
    <!-- Навигация по вкладкам -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('event.rating', $event->event_id) }}">
                        📊 Рейтинг
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('event.description', $event->event_id) }}">
                        📝 Описание
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('event.rules', $event->event_id) }}">
                        ⚙️ Правила
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('event.games', $event->event_id) }}">
                        🎮 История игр
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Список игр -->
    @if($games->count() > 0)
        @foreach($games as $game)
            @php
                $stats = ['ron' => 0, 'tsumo' => 0, 'draw' => 0, 'abortive' => 0];
                if(isset($game->rounds)) {
                    foreach ($game->rounds as $round) {
                        if (in_array($round->round_end_type, ['ron', 'tsumo', 'draw', 'abortive-draw'])) {
                            $stats[$round->round_end_type === 'abortive-draw' ? 'abortive' : $round->round_end_type]++;
                        } else {
                            $stats['draw']++;
                        }
                    }
                }
                $finalScores = [];
                if (isset($game->rounds) && $game->rounds->isNotEmpty()) {
                    $lastRound = $game->rounds->last();
                    if(isset($lastRound->results)) {
                        foreach ($lastRound->results as $result) {
                            $finalScores[$result->user_id] = $result->points_sum ?? 0;
                        }
                    }
                }
            @endphp

            <div class="card mb-3 game-card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="text-muted small mb-2">{{ date('d.m.Y', strtotime($game->game_date)) }}</div>
                            <div class="d-flex flex-wrap gap-3">
                                @if(isset($game->rounds) && $game->rounds->isNotEmpty() && isset($game->rounds->first()->results))
                                    @foreach($game->rounds->first()->results as $result)
                                        @php
                                            $score = $finalScores[$result->user_id] ?? 0;
                                            $scoreClass = $score >= 0 ? 'text-success' : 'text-danger';
                                        @endphp
                                        <div>
                                            <strong>{{ $result->user_name ?? 'Неизвестный' }}</strong>
                                            <span class="{{ $scoreClass }} fw-bold">{{ $score >= 0 ? '+' : '' }}{{ number_format($score) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-muted">Нет данных об игроках</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <span class="badge bg-secondary me-2">🎲 {{ isset($game->rounds) ? $game->rounds->count() : 0 }} раундов</span>
                            <button class="btn btn-sm btn-outline-secondary toggle-rounds" data-game-id="{{ $game->game_id }}">
                                📊 Ron: {{ $stats['ron'] }} | Tsumo: {{ $stats['tsumo'] }} | Draw: {{ $stats['draw'] }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounds-details collapse" id="rounds-{{ $game->game_id }}">
                    <div class="card-body pt-0 border-top">
                        <div class="small">
                            @if(isset($game->rounds))
                                @foreach($game->rounds as $round)
                                    <div class="round-item py-2 border-bottom">
                                        <div class="fw-bold">{{ $round->round_name ?? 'Неизвестный раунд' }}</div>
                                        @if($round->round_end_type == 'abortive-draw')
                                            @php $chomboPlayer = isset($round->results) ? $round->results->where('chombo', 1)->first() : null; @endphp
                                            <div class="text-danger">⚠️ Перераздача (чомбо) — {{ $chomboPlayer->user_name ?? 'Неизвестный' }}</div>
                                        @elseif(in_array($round->round_end_type, ['ron', 'tsumo']))
                                            @php $winner = isset($round->results) ? $round->results->where('points_change', '>', 0)->first() : null; @endphp
                                            @if($winner)
                                                <div>
                                                    🏆 {{ $winner->user_name ?? 'Неизвестный' }} — {{ $round->round_end_type == 'ron' ? 'на сбросе' : 'цумо' }}
                                                    @if(isset($winner->riichi_bet) && $winner->riichi_bet) <span class="badge bg-danger">ричи</span> @endif
                                                    <span class="float-end">{{ isset($winner->points_change) && $winner->points_change > 0 ? '+' : '' }}{{ $winner->points_change ?? 0 }}</span>
                                                </div>
                                            @endif
                                            <div class="text-muted small">
                                                @if(isset($round->results))
                                                    @foreach($round->results->where('points_change', '<', 0) as $loser)
                                                        {{ $loser->user_name ?? 'Неизвестный' }}: {{ $loser->points_change ?? 0 }}
                                                        @if(isset($loser->riichi_bet) && $loser->riichi_bet) <span class="badge bg-danger">ричи</span> @endif
                                                        @if(!$loop->last) | @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($round->round_end_type == 'draw')
                                            <div>🔄 Ничья (рюкёку)</div>
                                        @elseif($round->round_end_type == 'nagasi-mangan')
                                            <div>🌀 Нагаши манган</div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted">Нет данных о раундах</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4">
            {{ $games->withQueryString()->links() }}
        </div>
    @else
        <div class="alert alert-info">В этом событии пока нет сыгранных игр.</div>
    @endif
</div>

<script>
    document.querySelectorAll('.toggle-rounds').forEach(button => {
        button.addEventListener('click', function() {
            const gameId = this.dataset.gameId;
            const detailsBlock = document.getElementById(`rounds-${gameId}`);
            if (detailsBlock) {
                detailsBlock.classList.toggle('show');
            }
        });
    });
</script>

<style>
    .game-card {
        transition: box-shadow 0.2s;
    }
    .game-card:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .round-item:last-child {
        border-bottom: none !important;
    }
    .rounds-details.collapse {
        display: none;
    }
    .rounds-details.collapse.show {
        display: block;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 12px 20px;
    }
    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-bottom: 2px solid #0d6efd;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
        border-bottom: 2px solid #0d6efd;
        background: transparent;
    }
</style>
@endsection