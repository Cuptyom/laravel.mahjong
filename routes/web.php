<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MyEventsController;
use App\Http\Controllers\CreateEventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CurrentGameController;
//главная
Route::get('/', [MainController::class, 'index'])->name('home');
//вход/рег
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


//профиль
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// Таблии ивентов
// Страницы события
// Рейтинг с сортировкой и фильтрацией
Route::get('/event/{event_id}/{sort?}/{filter?}', [EventController::class, 'rating'])
    ->where('sort', 'rating|avg_score')
    ->where('filter', 'all|min_games')
    ->name('event.rating');
Route::get('/event/{event_id}/description', [EventController::class, 'description'])->name('event.description');
Route::get('/event/{event_id}/rules', [EventController::class, 'rules'])->name('event.rules');
Route::get('/event/{event_id}/games', [EventController::class, 'games'])->name('event.games');
//удаление
// Удаление игры (только для admin и judge)
Route::delete('/event/{event_id}/game/{game_id}/delete', [EventController::class, 'deleteGame'])->name('event.delete_game');
//редачить
Route::get('/event/{event_id}/edit', [EventController::class, 'edit'])->name('event.edit');
Route::post('/event/{event_id}/update', [EventController::class, 'update'])->name('event.update');
// Приглашение игроков
Route::get('/event/{event_id}/invite_players', [EventController::class, 'invitePlayers'])->name('event.invite_players');
Route::post('/event/{event_id}/invite_players', [EventController::class, 'invitePlayerPost'])->name('event.invite_player.post');
// юзер манагмент

// Управление пользователями события (только для admin)
Route::get('/event/{event_id}/users_management', [EventController::class, 'usersManagement'])->name('event.users_management');
Route::post('/event/{event_id}/update_user_role', [EventController::class, 'updateUserRole'])->name('event.update_user_role');
Route::post('/event/{event_id}/remove_user', [EventController::class, 'removeUser'])->name('event.remove_user');


//my events
// Мои события (сайдбар)
Route::get('/my_events', [MyEventsController::class, 'index'])->name('my.events');
// сделать ивент

// Создание события
Route::get('/create_event', [CreateEventController::class, 'showForm'])->name('create_event.form');
Route::post('/create_event', [CreateEventController::class, 'store'])->name('create_event.store');

// Уведомления
Route::get('/my_notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/my_notifications/accept/{id}', [NotificationController::class, 'accept'])->name('notifications.accept');
Route::get('/my_notifications/reject/{id}', [NotificationController::class, 'reject'])->name('notifications.reject');

// Текущие игры
Route::get('/create_game', [CurrentGameController::class, 'createForm'])->name('current_game.create');
Route::get('/current_game/participants/{eventId}', [CurrentGameController::class, 'getParticipants']);
Route::post('/current_game/store', [CurrentGameController::class, 'store'])->name('current_game.store');
Route::get('/current_game/{eventId}', [CurrentGameController::class, 'show'])->name('current_game.show');
// Мои текущие игры
Route::get('/my_current_games', [CurrentGameController::class, 'myCurrentGames'])->name('current_game.my_games');

Route::post('/current_game/{eventId}/add_round', [CurrentGameController::class, 'addRound'])->name('current_game.add_round');

Route::post('/current_game/{eventId}/add_round', [CurrentGameController::class, 'addRound'])->name('current_game.add_round');