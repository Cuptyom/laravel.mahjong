@extends('layout')

@section('title', 'Описание - ' . $event->event_name)

@section('content')
<div class="container py-4">
    <!-- Навигация по вкладкам -->
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin ?? false])
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">{{ $event->event_name }}</h1>
        </div>
    </div>
    
    <!-- Описание -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    @if($event->event_description)
                        <p class="lead mb-0">{{ $event->event_description }}</p>
                    @else
                        <p class="text-muted mb-0">Описание отсутствует</p>
                    @endif
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
</style>
@endsection