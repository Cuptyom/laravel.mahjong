@extends('layout')

@section('title', 'Рейтинг - ' . $event->event_name)

@section('content')
<div class="container py-4">
        <!-- Навигация по вкладкам -->
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin ?? false])
    
    <!-- Заголовок -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 fw-bold">{{ $event->event_name }}</h1>
        <div class="mt-2">
            @if($event->rating_table_visability == 1)
                <span class="badge bg-success">
                    🌍 Рейтинг доступен всем
                </span>
            @else
                <span class="badge bg-warning text-dark">
                    🔒 Рейтинг только для участников
                </span>
            @endif
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
                                    <th class="text-end">Рейтинг</th>
                                    <th class="text-end pe-4">Сыграно игр</th>
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
                                                {{ number_format($player->rating) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ $player->games_played }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            Нет данных о рейтинге. Ещё не сыграно ни одной игры.
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

<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
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