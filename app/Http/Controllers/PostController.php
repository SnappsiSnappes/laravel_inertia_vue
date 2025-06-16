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

            try {
                // Парсим JSON-тело статьи
                $parsedBody = json_decode($post->body, true);

                // Проверяем структуру parsedBody
                if (isset($parsedBody['blocks'])) {
                    // Инициализируем переменные для текста и картинки
                    $fullText = '';
                    $firstImage = null;

                    // Проходим по всем блокам
                    foreach ($parsedBody['blocks'] as $block) {
                        switch ($block['type']) {
                            case 'paragraph':
                                // Если блок — параграф, добавляем его текст
                                $fullText .= $block['data']['text'] . ' ';
                                break;

                            case 'header':
                                // Если блок — заголовок, добавляем его текст
                                $fullText .= $block['data']['text'] . ' ';
                                break;

                            case 'list':
                                // Если блок — список, объединяем элементы в строку
                                $fullText .= implode(' ', array_column($block['data']['items'], 'content')) . ' ';
                                break;

                            case 'image':
                                // Если блок — изображение, сохраняем первую картинку
                                if (!$firstImage && isset($block['data']['file']['url'])) {
                                    $firstImage = $block['data']['file']['url'];
                                }
                                break;

                            default:
                                // Пропускаем другие типы блоков
                                break;
                        }
                    }

                    // Обрезаем текст до 50 слов
                    $post->previewText = Str::words(trim($fullText), 50, '...');

                    // Сохраняем URL первой картинки
                    $post->previewImage = $firstImage;
                }
            } catch (\Exception $e) {
                \Log::error('Error parsing post body:', ['post_id' => $post->id, 'error' => $e->getMessage()]);
            }
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

        // Создание поста с user_id
        $post = Post::create([
            'user_id' => Auth::id(), // Добавьте user_id
            'title' => $validated['title'],
            'body' => $validated['body'],
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
