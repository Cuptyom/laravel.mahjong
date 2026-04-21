@extends('layout')

@section('title', 'Мои события')

@section('content')
<div class="container-fluid px-0">
    <h1 class="mb-4">Мои события</h1>
    
    @if(!$isAuthenticated)
        <div class="alert alert-warning">
             Вы не вошли в систему. 
            <a href="{{ route('login') }}" class="alert-link">Войдите</a>, чтобы просмотреть свои события.
        </div>
    @elseif($events->count() > 0)
        <div class="list-group">
            @foreach($events as $event)
                <a href="{{ route('event.rating', $event->event_id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $event->event_name }}</h5>
                            <p class="mb-1 text-muted small">
                                Тип: {{ $event->event_type }} | 
                                Стартовый счёт: {{ number_format($event->start_score) }} |
                                Мин. игр: {{ $event->min_games }}
                            </p>
                            @if($event->event_description)
                                <p class="mb-0 small">{{ \Illuminate\Support\Str::limit($event->event_description, 100) }}</p>
                            @endif
                        </div>
                        <div class="text-end">
                            @if($event->isEnd)
                                <span class="badge bg-danger mb-2">Завершён</span>
                            @else
                                <span class="badge bg-success mb-2">Активен</span>
                            @endif
                            <br>
                            <small class="text-muted">ID: {{ $event->event_id }}</small>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Пагинация -->
        <div class="d-flex justify-content-center mt-4">
            {{ $events->withQueryString()->links() }}
        </div>
        
    @else
        <div class="alert alert-info">
            Вы пока не участвуете ни в одном событии.
            <a href="{{ route('home') }}" class="alert-link">Перейти к доступным рейтингам</a>
        </div>
    @endif
</div>
@endsection