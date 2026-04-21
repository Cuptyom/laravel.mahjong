<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mahjong Rating')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f5f5f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        footer {
            margin-top: auto;
            border-top: 1px solid #dee2e6;
        }
        /* Стили для сайдбара */
        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .main-content {
            min-height: calc(100vh - 140px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                🀄 Mahjong Rating
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php
                        $userId = request()->cookie('user_id');
                        $user = null;
                        if ($userId) {
                            $user = DB::table('users')->where('user_id', $userId)->first();
                        }
                    @endphp
                    
                    @if($user)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                 {{ $user->user_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Мой профиль</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}">Выйти</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white px-3 ms-2" href="{{ route('login') }}" style="border-radius: 20px;">Войти</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <div class="row">
            <!-- Сайдбар -->
            @if(!in_array(Route::currentRouteName(), ['login', 'register']))
                <div class="col-md-3">
                    <div class="sidebar">
                        <h6 class="text-muted mb-3">МЕНЮ</h6>
                        <nav class="nav flex-column">
                            <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}">
                                 Главная
                            </a>
                            @php
                            $unreadNotificationsCount = 0;
                                if ($user) {
                                    $unreadNotificationsCount = App\Http\Controllers\NotificationController::getUnreadCount();
                                }
                            @endphp
                            @if($user)
                                <a class="nav-link {{ Route::currentRouteName() == 'my.events' ? 'active' : '' }}" href="{{ route('my.events') }}">
                                     Мои события
                                </a>
                                <a class="nav-link {{ Route::currentRouteName() == 'create_event.form' ? 'active' : '' }}" href="{{ route('create_event.form') }}">
                                     Создать событие
                                </a>
                                <a class="nav-link {{ Route::currentRouteName() == 'profile.show' ? 'active' : '' }}" href="{{ route('profile.show') }}">
                                     Редактировать профиль
                                </a>
                                <a class="nav-link {{ Route::currentRouteName() == 'notifications.index' ? 'active' : '' }}" 
                               href="{{ route('notifications.index') }}" 
                               style="position: relative;">
                                 Уведомления
                                @if($unreadNotificationsCount > 0)
                                    <span style="position: absolute; top: 8px; right: 30px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 15px; font-weight: bold; min-width: 18px; text-align: center;">
                                        {{ $unreadNotificationsCount }}
                                    </span>
                                @endif
                            </a>
                            @endif
                        </nav>
                    </div>
                </div>
                <div class="col-md-9">
                    @yield('content')
                </div>
            @else
        <div class="col-12">
        @yield('content')
    </div>
@endif
        </div>
    </div>
    
    <footer class="bg-white py-4 mt-5">
        <div class="container text-center text-muted">
            <small>Mahjong Rating System &copy; {{ date('Y') }}</small>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>