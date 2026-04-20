@extends('layout')

@section('title', 'Управление пользователями - ' . $event->event_name)

@section('content')
<div class="container-fluid px-0">
    @include('event.tabs', ['event' => $event, 'isAdmin' => $isAdmin, 'isParticipant' => $isParticipant])
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">👥 Управление пользователями</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>Логин</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participants as $participant)
                                    <tr>
                                        <td>{{ $participant->user_id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ asset('uploads/avatars/' . $participant->user_avatar) }}" 
                                                     alt="{{ $participant->user_name }}"
                                                     class="rounded-circle"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                                <span>{{ $participant->user_name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $participant->user_login }}</td>
                                        <td>{{ $participant->user_gmail }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('event.update_user_role', $event->event_id) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
                                                <select name="role" class="form-select form-select-sm" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                                                    <option value="player" {{ $participant->status == 'player' ? 'selected' : '' }}>Игрок</option>
                                                    <option value="judge" {{ $participant->status == 'judge' ? 'selected' : '' }}>Судья</option>
                                                    <option value="admin" {{ $participant->status == 'admin' ? 'selected' : '' }}>Администратор</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if($participant->user_id != request()->cookie('user_id'))
                                                <form method="POST" action="{{ route('event.remove_user', $event->event_id) }}" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите исключить этого пользователя из события?')">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        🗑️ Исключить
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Вы</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection