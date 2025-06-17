<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Гости
Route::middleware('guest')->group(function () {
    // Регистрация
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Вход
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login_post');
});

// Аутентифицированные пользователи
Route::middleware('auth')->group(function () {
    // Главная страница (dashboard)
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    // Выход
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Редактирование профиля
    Route::inertia('/edit', 'Auth/Edit')->name('edit');
    Route::post('/edit', [AuthController::class, 'edit']);

    // Удаление пользователя
    Route::post('/delete_user', [AuthController::class, 'delete_user'])->name('delete_user');

    // Получение данных пользователя для /edit
    Route::get('/user', function () {
        return request()->user()->only('id', 'name', 'avatar', 'email');
    });



    Route::post('/upload-image', function (Request $request) {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $path = $request->file('image')->store('images', 'public');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => Storage::disk('public')->url($path),
            ],
        ]);
    });
});

// Главная страница
Route::inertia('/', 'Home')->name('home');

// О нас
Route::inertia('/about', 'About')->name('about');

// Показать всех пользователей
Route::get('/all_users', [AuthController::class, 'showAllUsers'])->name('all_users');


// Маршруты для постов
Route::resource('posts', PostController::class);
