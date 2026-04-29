@extends('layout')

@section('title', 'Редактирование - ' . $event->event_name)

@section('content')
<div class="container-fluid px-0">
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin, 'isParticipant' => $isParticipant])
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Редактирование события</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('event.update', $event->event_id) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Название события</label>
                            <input type="text" name="event_name" class="form-control" 
                                   value="{{ $event->event_name }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="event_description" class="form-control" rows="4">{{ $event->event_description }}</textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Стартовые очки</label>
                                <input type="number" name="start_score" class="form-control" 
                                       value="{{ $event->start_score }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Минимальное количество игр</label>
                                <input type="number" name="min_games" class="form-control" 
                                       value="{{ $event->min_games }}" min="0" required>
                                <small class="text-muted">Минимальное количество игр для отображения в фильтре "С минимумом игр"</small>
                            </div>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Настройки Ума (дополнительные очки за места)</h6>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">1 место</label>
                                <input type="number" name="uma_1st" class="form-control" 
                                       value="{{ $event->uma_1st }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">2 место</label>
                                <input type="number" name="uma_2nd" class="form-control" 
                                       value="{{ $event->uma_2nd }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">3 место</label>
                                <input type="number" name="uma_3rd" class="form-control" 
                                       value="{{ $event->uma_3rd }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">4 место</label>
                                <input type="number" name="uma_4th" class="form-control" 
                                       value="{{ $event->uma_4th }}" required>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            <a href="{{ route('event.rating', $event->event_id) }}" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection