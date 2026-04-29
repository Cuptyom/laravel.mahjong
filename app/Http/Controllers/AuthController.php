<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    //логин
    public function showLogin()
    {
        return view('auth/login');
    }

    public function login(Request $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');
        

        $user = DB::table('users')
            ->where('user_login', $login)
            ->orWhere('user_gmail', $login)
            ->first();
        if(!$user){
            return back()->with('error', 'Неверный Логин или Пароль!');
        }
        if($user->user_status == 'Blocked'){
            return back()->with('error', 'Пользователь заблокирован!');
        }

        if ($user && $password == $user->user_pass && $user->user_status != 'Blocked') {
            Cookie::queue('user_id', $user->user_id, 43200); // кукезззз
            return redirect('/')->with('success', 'Добро пожаловать, ' . $user->user_name . '!');
        }
        
        return back()->with('error', 'Неверный логин или пароль');
    }
    
    // Показать форму регистрации
    public function showRegister()
    {
        return view('auth/register');
    }
    
    public function register(Request $request)
    {
        $email = $request->input('email');
        $login = $request->input('login');
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');
        
        if (!$email || !$login || !$password || !$passwordConfirmation) {
            return back()->with('error', 'Заполните все поля');
        }
        
        //регулярка
        $emailPattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/';
        if (!preg_match($emailPattern, $email)) {
            return back()->with('error', 'Введите корректный email (например: name@domain.com)');
        }


        if ($password !== $passwordConfirmation) {
            return back()->with('error', 'Пароли не совпадают');
        }
        
        if (strlen($password) < 4) {
            return back()->with('error', 'Пароль должен быть не менее 4 символов');
        }
        
        $emailExists = DB::table('users')->where('user_gmail', $email)->exists();
        if ($emailExists) {
            return back()->with('error', 'Пользователь с таким email уже существует');
        }
        
        $loginExists = DB::table('users')->where('user_login', $login)->exists();
        if ($loginExists) {
            return back()->with('error', 'Пользователь с таким логином уже существует');
        }
        
        // запрос регистрасии
        $userId = DB::table('users')->insertGetId([
            'user_login' => $login,
            'user_pass' => $password,
            'user_name' => $login,
            'user_gmail' => $email,
            'user_country' => null,
            'user_city' => null,
            'user_phone' => null,
            'user_avatar' => 'default.jpg',
            'user_status' => null,
        ]);
        
        // куки
        Cookie::queue('user_id', $userId, 43200);
        
        return redirect('/')->with('success', 'Регистрация прошла успешно! Добро пожаловать, ' . $login . '!');
    }
    
    // Выход -куки
    public function logout()
    {
        Cookie::queue(Cookie::forget('user_id'));
        return redirect('/')->with('success', 'Вы вышли из системы');
    }
}