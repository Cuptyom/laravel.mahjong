@extends('layout')

@section('title', 'Приглашение игроков - ' . $event->event_name)

@section('content')
<div class="container-fluid px-0">
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin, 'isParticipant' => $isParticipant])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Приглашение игроков</h5>
                </div>
                @if(($isLocal && $isPracticant) || $isAdmin)
                    <div class="card-body">
                        <form method="GET" action="{{ route('event.invite_players', $event->event_id) }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Поиск по логину или имени пользователя..." 
                                       value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit">Найти</button>
                                @if($search)
                                    <a href="{{ route('event.invite_players', $event->event_id) }}" class="btn btn-outline-secondary">Сбросить</a>
                                @endif
                            </div>
                        </form>
                        
                        @if($search)
                            @if($users->count() > 0)
                                <div class="list-group">
                                    @foreach($users as $user)
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $user->user_name }}</strong>
                                                    <span class="text-muted ms-2">({{ $user->user_login }})</span>
                                                </div>
                                                <div>
                                                    @if($user->alreadyParticipant)
                                                        <span class="badge bg-success me-2">Уже участвует</span>
                                                    @elseif($user->alreadyInvited)
                                                        <span class="badge bg-warning me-2">Уже приглашён</span>
                                                    @else
                                                        <form method="POST" action="{{ route('event.invite_player.post', $event->event_id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                                                            <button type="submit" class="btn btn-primary btn-sm">Пригласить</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    По запросу "{{ $search }}" пользователи не найдены.
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-5">
                                <p>Введите имя или логин пользователя для поиска</p>
                            </div>
                        @endif
                    </div>
                @else
                <div class="text-center text-muted py-5">
                    вам нельзя приглашать учатников
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection