<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    // Показать профиль
    public function show()
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $user = DB::table('users')->where('user_id', $userId)->first();
        
        if (!$user) {
            return redirect('/login')->with('error', 'Пользователь не найден');
        }
        
        return view('profile/profile', compact('user'));
    }
    
    // Обновить профиль
    public function update(Request $request)
    {
        $userId = request()->cookie('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Войдите в систему');
        }
        
        $userName = $request->input('user_name');
        $country = $request->input('user_country');
        $city = $request->input('user_city');
        $phone = $request->input('user_phone');
        
        // Валидация
        if (empty($userName)) {
            return back()->with('error', 'Имя не может быть пустым');
        }
        
        // Получаем текущего пользователя
        $user = DB::table('users')->where('user_id', $userId)->first();
        
        // Обработка аватара
        $avatarName = $user->user_avatar; // сохраняем текущий аватар по умолчанию
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // Проверка типа файла
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = $file->getClientOriginalExtension();
            
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return back()->with('error', 'Разрешены только изображения (jpg, png, gif, webp)');
            }
            
            // Проверка размера (макс 2MB)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return back()->with('error', 'Размер файла не должен превышать 2MB');
            }
            
            // Генерируем имя файла: логин_avatar.jpg
            $avatarName = $user->user_login . '_avatar.' . $extension;
            
            // Сохраняем файл
            $file->move(public_path('uploads/avatars'), $avatarName);
            
            // Удаляем старый аватар, если он не default.jpg
            if ($user->user_avatar && $user->user_avatar != 'default.jpg') {
                $oldPath = public_path('uploads/avatars/' . $user->user_avatar);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        }
        
        // Обновляем данные пользователя
        DB::table('users')->where('user_id', $userId)->update([
            'user_name' => $userName,
            'user_country' => $country ?: null,
            'user_city' => $city ?: null,
            'user_phone' => $phone ?: null,
            'user_avatar' => $avatarName,
        ]);
        
        return back()->with('success', 'Профиль успешно обновлён');
    }
}