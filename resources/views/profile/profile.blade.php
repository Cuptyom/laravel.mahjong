@extends('layout')

@section('title', 'Профиль - ' . $user->user_name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Мой профиль</h2>
                    
                    <!-- Аватар -->
                    <div class="text-center mb-4">
                        @php
                            $avatarPath = asset('uploads/avatars/' . ($user->user_avatar ?? 'default.jpg'));
                        @endphp
                        <img src="{{ $avatarPath }}" 
                             alt="Avatar" 
                             class="rounded-circle"
                             style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;">
                    </div>
                    
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- user_id (только для чтения) -->
                        <div class="mb-3">
                            <label class="form-label">ID пользователя</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->user_id }}" readonly disabled>
                        </div>
                        
                        <!-- Логин (только для чтения) -->
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->user_login }}" readonly disabled>
                        </div>
                        
                        <!-- Email (только для чтения) -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->user_gmail }}" readonly disabled>
                        </div>
                        
                        <!-- Имя (редактируется) -->
                        <div class="mb-3">
                            <label class="form-label">Имя *</label>
                            <input type="text" name="user_name" class="form-control" value="{{ old('user_name', $user->user_name) }}" required>
                        </div>
                        
                        <!-- Страна -->
                        <div class="mb-3">
                            <label class="form-label">Страна</label>
                            <input type="text" name="user_country" class="form-control" value="{{ old('user_country', $user->user_country) }}">
                        </div>
                        
                        <!-- Город -->
                        <div class="mb-3">
                            <label class="form-label">Город</label>
                            <input type="text" name="user_city" class="form-control" value="{{ old('user_city', $user->user_city) }}">
                        </div>
                        
                        <!-- Телефон -->
                        <div class="mb-3">
                            <label class="form-label">Телефон</label>
                            <input type="tel" name="user_phone" class="form-control" value="{{ old('user_phone', $user->user_phone) }}">
                            <small class="text-muted">Формат: 79001234567</small>
                        </div>
                        
                        <!-- Аватар -->
                        <div class="mb-4">
                            <label class="form-label">Аватар</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-muted">Максимум 2MB. Поддерживаются: JPG, PNG, GIF, WEBP</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection