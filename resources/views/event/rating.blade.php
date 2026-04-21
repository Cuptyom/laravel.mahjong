@extends('layout')

@section('title', 'Рейтинг - ' . $event->event_name)

@section('content')
<div class="container-fluid px-0">
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin, 'isParticipant' => $isParticipant, ])
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">{{ $event->event_name }}</h1>
            <div class="mt-2">
                @if($event->rating_table_visability == 1)
                    <span class="badge bg-success">🌍 Рейтинг доступен всем</span>
                @else
                    <span class="badge bg-warning text-dark">🔒 Рейтинг только для участников</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Панель сортировки и фильтрации -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="btn-group" role="group">
                            <a href="/event/{{ $event->event_id }}/rating/all" 
                               class="btn btn-sm {{ $sort == 'rating' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                📊 По рейтингу
                            </a>
                            <a href="/event/{{ $event->event_id }}/avg_score/all" 
                               class="btn btn-sm {{ $sort == 'avg_score' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                📈 По средним очкам
                            </a>
                        </div>
                        
                        <div class="btn-group" role="group">
                            <a href="/event/{{ $event->event_id }}/{{ $sort }}/all" 
                               class="btn btn-sm {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                👥 Все игроки
                            </a>
                            @if($event->min_games > 0)
                                <a href="/event/{{ $event->event_id }}/{{ $sort }}/min_games" 
                                   class="btn btn-sm {{ $filter == 'min_games' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    🎯 С минимумом игр (≥{{ $event->min_games }})
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Таблица рейтинга -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">#</th>
                                    <th>Игрок</th>
                                    <th class="text-end">
                                        @if($sort == 'rating')
                                            Рейтинг
                                        @else
                                            Средние очки
                                        @endif
                                    </th>
                                    <th class="text-end pe-4">Сыграно игр</th>
                                    <th class="text-center">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratingList as $index => $player)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ asset('uploads/avatars/' . $player->user_avatar) }}" 
                                                     alt="{{ $player->user_name }}"
                                                     class="rounded-circle"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <span class="fw-medium">{{ $player->user_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <span class="badge bg-primary fs-6 px-3 py-2">
                                                @if($sort == 'rating')
                                                    {{ number_format($player->rating) }}
                                                @else
                                                    {{ number_format($player->avg_score, 2) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ $player->games_played }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($player->is_excluded)
                                                <span class="badge bg-danger">Исключён</span>
                                            @else
                                                <span class="badge bg-success">Участник</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            Нет данных о рейтинге.
                                            @if($filter == 'min_games' && $event->min_games > 0)
                                                Нет игроков с количеством игр не менее {{ $event->min_games }}.
                                            @else
                                                Ещё не сыграно ни одной игры.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection