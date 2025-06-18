<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User; // Импортируем модель User
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


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
            $post->preview_image != null 
                ? '/storage/' . $post->preview_image 
                : null;
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
        // Проверяем, может ли пользователь редактировать пост
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Пожалуйста, авторизуйтесь, чтобы создать пост.');
        }

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



public function ChangeReaction(Request $request, $postId)
{
    // Проверяем, авторизован ли пользователь
    if (!auth()->check()) {
        return response()->json(['message' => 'Пользователь не авторизован.'], 401);
    }

    $request->validate([
        'sticker_id' => 'required|string',
    ]);

    $userId = auth()->id(); // Получаем ID авторизованного пользователя



    $stickerId = $request->input('sticker_id');

    // Проверяем, существует ли реакция
    $reaction = PostReaction::where('post_id', $postId)
        ->where('sticker_id', $stickerId)
        ->where('user_id', $userId)
        ->first();

    if ($reaction) {
        // Удаляем реакцию, если она уже существует
        $reaction->delete();
        return response()->json(['message' => 'Реакция удалена.', 'removed' => true]);
    }


$data = [
    'post_id' => $postId,
    'sticker_id' => $stickerId,
    'user_id' => $userId,
    'ip_address' => $request->ip(),
];

Log::info('Data before creating reaction:', $data);

PostReaction::create($data);

    return response()->json(['message' => 'Реакция добавлена.', 'removed' => false]);
}

public function getReactions($postId)
    {
        $post = Post::with('reactions')->findOrFail($postId);

        // Группируем реакции по sticker_id
        $reactions = $post->reactions->groupBy('sticker_id')->map(function ($group) {
            return [
                'sticker_id' => $group->first()->sticker_id,
                'count' => $group->count(),
            ];
        });

        return response()->json($reactions);
    }
}
