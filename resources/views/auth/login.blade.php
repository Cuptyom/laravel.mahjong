@extends('layout')

@section('title', 'Вход')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Вход</h2>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Email или Логин</label>
                            <input type="text" name="login" class="form-control" placeholder="example@mail.com или username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Войти</button>
                        
                        <p class="text-center mb-0">
                            Нет аккаунта? 
                            <a href="{{ route('register') }}" class="text-decoration-none">Зарегистрироваться</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection