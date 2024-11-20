<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

// posts resource
Route::resource('posts',PostController::class);


// guests
Route::middleware('guest')->group(function () {

    // Register and login routes for guests
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);


    // login
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login_post');
});


// auth users only
Route::middleware('auth')->group(function () {

    // dashboard
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');


    // logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


    // edit user
    Route::inertia('/edit', 'Auth/Edit')->name('edit');
    Route::post('/edit', [AuthController::class, 'edit']);


    // delete user
    Route::post('/delete_user', [AuthController::class, 'delete_user'])->name('delete_user');
    
    
    //api json method for get user info, used in /edit
    Route::get('/user', function (Request $request) {
        return $request->user()->only('id', 'name', 'avatar', 'email');
    });


});

// home
Route::inertia('/', 'Home')->name('home');

// about
Route::inertia('/about', 'About')->name('about');

// all_users
Route::get('/all_users', [AuthController::class,'showAllUsers'])->name('all_users');

