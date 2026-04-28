@extends('layout')

@section('title', 'Мои текущие игры')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Мои текущие игры</h1>
    
    @if($currentGames->count() > 0)
        <div class="row">
            @foreach($currentGames as $game)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ $game->event_name }}</h5>
                            <small class="text-muted">{{ date('d.m.Y H:i', strtotime($game->game_date)) }}</small>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Позиция</th>
                                            <th>Игрок</th>
                                            <th class="text-end">Очки</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($game->players as $player)
                                            <tr>
                                                <td>
                                                    @if($player->start_position == 'East')
                                                        Восток
                                                    @elseif($player->start_position == 'South')
                                                        Юг
                                                    @elseif($player->start_position == 'West')
                                                        Запад
                                                    @else
                                                        Север
                                                    @endif
                                                </td>
                                                <td>{{ $player->user_name }}</td>
                                                <td class="text-end fw-bold">{{ number_format($player->points_sum) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('current_game.show', $game->event_id) }}" class="btn btn-primary w-100">
                                Продолжить игру
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            У вас нет активных игр.
            <a href="{{ route('current_game.create') }}" class="alert-link">Создать новую игру</a>
        </div>
    @endif
</div>
@endsection