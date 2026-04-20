<!-- Навигация по вкладкам события -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('event.rating') ? 'active' : '' }}" 
                   href="{{ route('event.rating', $event->event_id) }}">
                    📊 Рейтинг
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('event.description') ? 'active' : '' }}" 
                   href="{{ route('event.description', $event->event_id) }}">
                    📝 Описание
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('event.rules') ? 'active' : '' }}" 
                   href="{{ route('event.rules', $event->event_id) }}">
                    ⚙️ Правила
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('event.games') ? 'active' : '' }}" 
                   href="{{ route('event.games', $event->event_id) }}">
                    🎮 История игр
                </a>
            </li>
            @if($isParticipant ?? false)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('event.invite_players') ? 'active' : '' }}" 
                       href="{{ route('event.invite_players', $event->event_id) }}">
                        📧 Пригласить
                    </a>
                </li>
            @endif
            @if($isAdmin ?? false)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('event.edit') ? 'active' : '' }}" 
                       href="{{ route('event.edit', $event->event_id) }}">
                        ⚙️ Управление
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>