<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    // Register and login routes for guests
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login_post');
});

Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::inertia('/edit', 'Auth/Edit')->name('edit');
    Route::post('/edit', [AuthController::class, 'edit']);

    Route::get('/user', function (Request $request) {
        return $request->user()->only('id', 'name', 'avatar', 'email');
    });
});

Route::get('/', function (Request $request) {
    return Inertia('Home', [
        'users' => User::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })->paginate(9)->withQueryString(),

        'searchTerm' => $request->search,
        'can' => [
            'delete_user' => Auth::user()
                ? Auth::user()->can('delete', User::class)
                : null
        ]
    ]);
})->name('home');


Route::inertia('/about', 'About')->name('about');

// here
Route::post('/delete_user', [AuthController::class, 'delete_user'])->name('delete_user');