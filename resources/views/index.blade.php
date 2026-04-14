@extends('layout')

@section('title', 'Главная - Рейтинги маджонга')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Доступные рейтинги</h1>
            
            <!-- Поисковая строка -->
            <form method="GET" action="{{ route('home') }}" class="mb-4">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Поиск рейтинга по названию..."
                        value="{{ $search ?? '' }}"
                    >
                    <button class="btn btn-primary" type="submit">Найти</button>
                    @if($search ?? false)
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Сбросить</a>
                    @endif
                </div>
            </form>
            
            <!-- Список рейтингов -->
            @if($events->count() > 0)
                <div class="list-group mb-4">
                    @foreach($events as $event)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">{{ $event->event_name }}</h5>
                                    <p class="mb-1 text-muted small">
                                        Тип: {{ $event->event_type }} | 
                                        Стартовый рейтинг: {{ number_format($event->start_rating) }} |
                                        Мин. игр: {{ $event->min_games }}
                                    </p>
                                    @if($event->event_description)
                                        <p class="mb-0 small">{{ Str::limit($event->event_description, 100) }}</p>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success mb-2">Активен</span>
                                    <small class="d-block text-muted">ID: {{ $event->event_id }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Пагинация -->
                <div class="d-flex justify-content-center">
                    {{ $events->withQueryString()->links() }}
                </div>
                
            @else
                <div class="alert alert-info">
                    @if($search ?? false)
                        По запросу "{{ $search }}" ничего не найдено.
                    @else
                        Нет доступных рейтингов.
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection