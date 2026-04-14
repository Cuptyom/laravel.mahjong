@extends('layout')

@section('title', 'Правила - ' . $event->event_name)

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
                    <a class="nav-link active" href="{{ route('event.rules', $event->event_id) }}">
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
        </div>
    </div>
    
    <!-- Правила -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h4 mb-0">Минимум игр</h3>
                    </div>
                    <p class="display-6 fw-bold text-primary mb-0">{{ $event->min_games }}</p>
                    <small class="text-muted">необходимо для участия в рейтинге</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h4 mb-0">Стартовые очки</h3>
                    </div>
                    <p class="display-6 fw-bold text-success mb-0">{{ number_format($event->start_score) }}</p>
                    <small class="text-muted">начальное значение для всех игроков</small>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <h3 class="h4 mb-0">Система УМА (бонусы за места)</h3>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th style="width: 25%">1-е место</th>
                                    <th style="width: 25%">2-е место</th>
                                    <th style="width: 25%">3-е место</th>
                                    <th style="width: 25%">4-е место</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td class="fw-bold text-success">
                                        {{ number_format($event->uma_1st) }}
                                    </td>
                                    <td class="fw-bold text-info">
                                        {{ number_format($event->uma_2nd) }}
                                    </td>
                                    <td class="fw-bold text-warning">
                                        {{ number_format($event->uma_3rd) }}
                                    </td>
                                    <td class="fw-bold text-danger">
                                        {{ number_format($event->uma_4th) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-3">
                        * UMA начисляются после каждой игры в зависимости от занятого места
                    </small>
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
        color: #0d6efd;
        font-weight: 600;
        border-bottom: 2px solid #0d6efd;
        background: transparent;
    }
    .table-bordered td, .table-bordered th {
        font-size: 1.1rem;
        padding: 12px;
    }
</style>
@endsection