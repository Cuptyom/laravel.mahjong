@extends('layout')

@section('title', 'Регистрация')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Регистрация</h2>
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" placeholder="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" placeholder="минимум 4 символа" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Повторите пароль</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Зарегистрироваться</button>
                        
                        <p class="text-center mb-0">
                            Уже есть аккаунт? 
                            <a href="{{ route('login') }}" class="text-decoration-none">Войти</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection