<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

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

    // Маршруты для постов
    Route::resource('posts', PostController::class);
});

// Главная страница
Route::inertia('/', 'Home')->name('home');

// О нас
Route::inertia('/about', 'About')->name('about');

// Показать всех пользователей
Route::get('/all_users', [AuthController::class, 'showAllUsers'])->name('all_users');