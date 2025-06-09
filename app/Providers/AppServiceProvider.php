<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post; // Импортируйте модель Post
use App\Policies\PostPolicy; // Импортируйте политику PostPolicy

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Регистрация политики для модели Post
        Post::class => PostPolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
