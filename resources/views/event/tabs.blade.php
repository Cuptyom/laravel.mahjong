<!-- Навигация по вкладкам события -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link"  
                   href="{{ route('event.rating', $event->event_id) }}">
                    📊 Рейтинг
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                   href="{{ route('event.description', $event->event_id) }}">
                    📝 Описание
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                   href="{{ route('event.rules', $event->event_id) }}">
                    ⚙️ Правила
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                   href="{{ route('event.games', $event->event_id) }}">
                    🎮 История игр
                </a>
            </li>
            @if($isAdmin ?? false)
            <li class="nav-item">
                <a class="nav-link"  
                   href="{{ route('event.edit', $event->event_id) }}">
                    ⚙️ Управление
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>