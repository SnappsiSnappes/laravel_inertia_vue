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
             $file = $request->file('preview_image');
 
             // Вычисляем хэш файла
             $hash = hash_file('sha256', $file->getRealPath());
 
             // Проверяем, существует ли файл с таким хэшом
             $path = "images/{$hash}";
             if (!Storage::disk('public')->exists($path)) {
                 // Если файла нет, сохраняем его с именем, равным хэшу
                 Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
             }
 
             // Генерируем полный URL для изображения
             $validated['preview_image'] = Storage::disk('public')->url($path);
         }
 
         // Создание поста с user_id
         $post = Post::create([
             'user_id' => Auth::id(),
             'title' => $validated['title'],
             'body' => $validated['body'],
             'preview_text' => $validated['preview_text'],
             'preview_image' => $validated['preview_image'] ?? null,
         ]);
 
         return redirect()->route('posts.show', $post)
             ->with('message', ['message' => 'Пост создан', 'type' => 'success']);
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

        if ($post->preview_image !== null and $post->preview_image !== '') {
            // $post->preview_image = '/storage/' . $post->preview_image;
        }


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

    public function update(UpdatePostRequest $request, Post $post)
    {
        // Проверяем, может ли пользователь обновлять пост
        if (!Auth::user()->can('isAdmin', User::class) && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        // Валидация данных
        $validated = $request->validated();
    
        // Обработка загруженного изображения
        if ($request->hasFile('preview_image')) {
            $file = $request->file('preview_image');
    
            // Вычисляем хэш файла
            $hash = hash_file('sha256', $file->getRealPath());
            $path = "images/{$hash}";
    
            // Удаляем старое изображение, если оно существует
            if ($post->preview_image) {
                $oldFilePath = str_replace('/storage/', '', $post->preview_image);
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
    
            // Сохраняем новое изображение
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            }
    
            // Генерируем полный URL для нового изображения
            $validated['preview_image'] = Storage::disk('public')->url($path);
        } elseif ($request->boolean('delete_preview_image')) {
            // Если нужно удалить изображение
            if ($post->preview_image) {
                $oldFilePath = str_replace('/storage/', '', $post->preview_image);
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
    
            // Устанавливаем preview_image в null
            $validated['preview_image'] = null;
        } else {
            // Если файл не загружен и флаг удаления не установлен, сохраняем старое значение
            $validated['preview_image'] = $post->preview_image;
        }
    
        // Обновление поста
        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'preview_text' => $validated['preview_text'],
            'preview_image' => $validated['preview_image'],
        ]);
    
        // Перенаправление с сообщением об успехе
        return redirect()->route('posts.show', $post)->with('message', ['message' => 'Пост обновлен', 'type' => 'success']);
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
        return redirect()->route('posts.index')->with('message',  ['message' => 'Пост удалён', 'type' => 'success']);
    }



    public function ChangeReaction(Request $request, $postId)
    {
        // Проверяем, авторизован ли пользователь
        if (!auth()->check()) {
            return response()->json([
                'message' => ['message' => 'Вы должны быть авторизованы, чтобы добавить реакцию.', 'type' => 'error'],
            ], 401);
        }

        try {
            $userId = auth()->id();
            $stickerId = $request->input('sticker_id');

            // Проверяем, существует ли реакция
            $reaction = PostReaction::where('post_id', $postId)
                ->where('sticker_id', $stickerId)
                ->where('user_id', $userId)
                ->first();

            if ($reaction) {
                // Удаляем реакцию
                $reaction->delete();

                return response()->json([
                    'message' => ['message' => 'Реакция удалена.', 'type' => 'success',],
                    'removed' => true,
                ]);
            }

            // Добавляем новую реакцию
            PostReaction::create([
                'post_id' => $postId,
                'sticker_id' => $stickerId,
                'user_id' => $userId,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => ['message' => 'Реакция добавлена', 'type' => 'success'],
                'removed' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' =>  ['message' => 'Произошла ошибка.', 'type' => 'error'],
            ], 500);
        }
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
