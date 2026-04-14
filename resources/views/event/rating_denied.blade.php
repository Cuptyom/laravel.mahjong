@extends('layout')

@section('title', 'Доступ запрещён - ' . $event->event_name)

@section('content')
<div class="container py-4">
    <!-- Навигация по вкладкам -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" style="pointer-events: none; color: #6c757d;">
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
            </ul>
        </div>
    </div>
    
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">{{ $event->event_name }}</h1>
            <div class="mt-2">
                <span class="badge bg-warning text-dark">
                    🔒 Рейтинг только для участников
                </span>
            </div>
        </div>
    </div>
    
    <!-- Сообщение о запрете доступа -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 bg-light">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <span class="display-1">🔒</span>
                    </div>
                    <h2 class="h3 mb-3">Вам не доступен этот рейтинг</h2>
                    <p class="text-muted mb-0">
                        Рейтинг этого события доступен только участникам.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
        background: transparent;
    }
</style>
@endsection