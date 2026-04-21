@extends('layout')

@section('title', 'Мои уведомления')

@section('content')
<div class="container-fluid px-0">
    <h1 class="mb-4">Уведомления</h1>
    
    @if($notifications->count() > 0)
        <div class="list-group">
            @foreach($notifications as $notification)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Приглашение в событие</h5>
                            <p class="mb-1">
                                Вас приглашают в <strong>{{ $notification->event_name }}</strong>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('notifications.accept', $notification->notification_id) }}" 
                               class="btn btn-success btn-sm me-2">
                                ✅ Принять
                            </a>
                            <a href="{{ route('notifications.reject', $notification->notification_id) }}" 
                               class="btn btn-danger btn-sm">
                                ❌ Отклонить
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            У вас нет новых уведомлений.
        </div>
    @endif
</div>
@endsection