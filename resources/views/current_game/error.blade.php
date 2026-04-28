@extends('layout')

@section('title', 'Ошибка')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-danger mb-3">Доступ запрещён</h3>
                    <p>{{ $message }}</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-3">На главную</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection