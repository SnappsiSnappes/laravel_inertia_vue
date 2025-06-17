<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Импортируем модель User
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */




    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(5);

        // Форматируем каждую статью
        $posts->each(function ($post) {
            // Добавляем человекочитаемую дату
            $post->humanReadableDate = Carbon::parse($post->created_at)->diffForHumans();
            $post->preview_image = '/storage/' . $post->preview_image;
        });

        return inertia('Posts/Posts', [
            'posts' => $posts,
            'authUser' => auth()->user(), // Передаем данные текущего пользователя
            'IsAdmin' => Auth::user()?->can('IsAdmin', User::class),
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return inertia('Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePostRequest $request)
    {

        // Валидация данных
        $validated = $request->validated();

        // Обработка загруженного изображения
        if ($request->hasFile('preview_image')) {
            $validated['preview_image'] = $request->file('preview_image')->store('images', 'public');
        }

        // Создание поста с user_id
        $post = Post::create([
            'user_id' => Auth::id(), // Добавьте user_id
            'title' => $validated['title'],
            'body' => $validated['body'],
            'preview_text' => $validated['preview_text'],
            'preview_image' => $validated['preview_image'] ?? null, // Если изображение не загружено, будет null
        ]);

        return redirect()->route('posts.index')->with('message', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Load the post with its associated user
        $post->load('user');

        // Format the date for better readability
        $post->humanReadableDate = Carbon::parse($post->created_at)->diffForHumans();
        $post->preview_image = '/storage/' . $post->preview_image;

        // Return the post data to the frontend
        return inertia('Posts/Show', [
            'post' => $post,
            'authUser' => auth()->user(), // Передаем данные текущего пользователя
            'IsAdmin' => Auth::user()?->can('IsAdmin', User::class),

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        // Проверяем, может ли пользователь редактировать пост
        if (!Auth::user()->can('isAdmin', User::class) && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Return the post data to the frontend
        return inertia('Posts/Edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // Проверяем, может ли пользователь обновлять пост
        if (!Auth::user()->can('isAdmin', User::class) && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Валидация данных (уже выполнена через UpdatePostRequest)
        $validated = $request->validated();

        // Обновление поста
        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        // Перенаправление с сообщением об успехе
        return redirect()->route('posts.index')->with('message', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Проверяем, может ли пользователь удалять пост
        if (!Auth::user()->can('isAdmin', User::class) && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Удаление поста
        $post->delete();

        // Перенаправление с сообщением об успехе
        return redirect()->route('posts.index')->with('message', 'Post deleted successfully!');
    }
}
