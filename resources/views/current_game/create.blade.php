@extends('layout')

@section('title', 'Создание игры')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Создание игры</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form id="createGameForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Выберите событие</label>
                            <select name="event_id" id="eventSelect" class="form-select" required>
                                <option value="">-- Выберите событие --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="playersSelection">
                            <h5 class="mt-4 mb-3">Распределение позиций</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">🀫 Восток (East)</label>
                                    <select name="player_east" id="playerEast" class="form-select" required disabled>
                                        <option value="">-- Сначала выберите событие --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">🀫 Юг (South)</label>
                                    <select name="player_south" id="playerSouth" class="form-select" required disabled>
                                        <option value="">-- Сначала выберите событие --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">🀫 Запад (West)</label>
                                    <select name="player_west" id="playerWest" class="form-select" required disabled>
                                        <option value="">-- Сначала выберите событие --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">🀫 Север (North)</label>
                                    <select name="player_north" id="playerNorth" class="form-select" required disabled>
                                        <option value="">-- Сначала выберите событие --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled> Создать игру</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const eventSelect = document.getElementById('eventSelect');
    const playerEast = document.getElementById('playerEast');
    const playerSouth = document.getElementById('playerSouth');
    const playerWest = document.getElementById('playerWest');
    const playerNorth = document.getElementById('playerNorth');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('createGameForm');
    
    // Функция проверки, выбран ли хотя бы один игрок (необязательно)
    function checkFormComplete() {
        // Проверяем, что все 4 игрока выбраны
        const allSelected = playerEast.value && playerSouth.value && playerWest.value && playerNorth.value;
        submitBtn.disabled = !allSelected;
    }
    
    // Добавляем слушатели на все select'ы
    [playerEast, playerSouth, playerWest, playerNorth].forEach(select => {
        select.addEventListener('change', checkFormComplete);
    });
    
    eventSelect.addEventListener('change', function() {
        const eventId = this.value;
        if (!eventId) {
            // Если событие не выбрано, блокируем все select'ы
            [playerEast, playerSouth, playerWest, playerNorth].forEach(select => {
                select.disabled = true;
                select.innerHTML = '<option value="">-- Сначала выберите событие --</option>';
            });
            submitBtn.disabled = true;
            return;
        }
        
        // Загружаем участников события
        fetch(`/current_game/participants/${eventId}`)
            .then(response => response.json())
            .then(participants => {
                // Очищаем и активируем select'ы
                [playerEast, playerSouth, playerWest, playerNorth].forEach(select => {
                    select.disabled = false;
                    select.innerHTML = '<option value="">-- Выберите игрока --</option>';
                });
                
                participants.forEach(participant => {
                    const option = `<option value="${participant.user_id}" ${participant.is_active ? 'disabled' : ''}>
                        ${participant.user_name} (${participant.user_login})${participant.is_active ? ' в игре' : ''}
                    </option>`;
                    playerEast.innerHTML += option;
                    playerSouth.innerHTML += option;
                    playerWest.innerHTML += option;
                    playerNorth.innerHTML += option;
                });
                
                submitBtn.disabled = true;
            });
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Блокируем кнопку, чтобы избежать двойной отправки
        submitBtn.disabled = true;
        submitBtn.textContent = 'Создание...';
        
        const formData = new FormData(form);
        
        fetch('{{ route("current_game.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.error) {
                alert(data.error);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Создать игру';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при создании игры');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Создать игру';
        });
    });
</script>
@endsection