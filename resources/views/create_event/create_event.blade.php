@extends('layout')

@section('title', 'Создание события')

@section('content')
<div class="container-fluid px-0">
    <h1 class="mb-4">Создание нового события</h1>
    
    @if(!$isAuthenticated)
        <div class="alert alert-warning">
            Создание события недоступно. 
            <a href="{{ route('login') }}" class="alert-link">Войдите в аккаунт</a>, чтобы создать событие.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('create_event.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Название события *</label>
                            <input type="text" name="event_name" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Тип события *</label>
                            <select name="event_type" class="form-select" required>
                                <option value="tournament">Турнир</option>
                                <option value="local">Локальное</option>
                                <option value="online">Онлайн</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="event_description" class="form-control" rows="4"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Стартовые очки *</label>
                            <input type="number" name="start_score" class="form-control" value="25000" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Минимальное количество игр</label>
                            <input type="number" name="min_games" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <h6 class="mt-3 mb-3">Настройки Ума (дополнительные очки за места)</h6>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">1 место</label>
                            <input type="number" name="uma_1st" class="form-control" value="10000" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">2 место</label>
                            <input type="number" name="uma_2nd" class="form-control" value="5000" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">3 место</label>
                            <input type="number" name="uma_3rd" class="form-control" value="-5000" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">4 место</label>
                            <input type="number" name="uma_4th" class="form-control" value="-10000" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="event_global_visability" class="form-check-input" value="1" checked>
                                <label class="form-check-label">Видимость события в глобальном списке</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="rating_table_visability" class="form-check-input" value="1" checked>
                                <label class="form-check-label">Рейтинг доступен всем (если выключено — только участникам)</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Создать событие</button>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Отмена</a>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection