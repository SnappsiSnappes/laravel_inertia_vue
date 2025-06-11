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


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */



public function index()
{
    $posts = Post::with('user')->latest()->paginate(5);

    // Format dates using Carbon and add human-readable date
    $posts->each(function ($post) {
        $post->humanReadableDate = Carbon::parse($post->created_at)->diffForHumans();

        // Parse the JSON body into blocks
        try {
            $parsedBody = json_decode($post->body, true);

            // Ensure the parsed body has the expected structure
            if (isset($parsedBody['blocks'])) {
                // Оставляем только первый блок
                $firstBlock = array_slice($parsedBody['blocks'], 0, 1);

                // Если первый блок — параграф, сокращаем текст
                foreach ($firstBlock as &$block) {
                    if ($block['type'] === 'paragraph') {
                        $block['data']['text'] = Str::words($block['data']['text'], 50, '...');
                    }
                }

                // Обновляем массив blocks, чтобы содержал только первый блок
                $parsedBody['blocks'] = $firstBlock;

                // Update the post's body with the shortened content
                $post->body = json_encode($parsedBody);
            }
        } catch (\Exception $e) {
            \Log::error('Error parsing post body:', ['post_id' => $post->id, 'error' => $e->getMessage()]);
        }
    });

    return inertia('Posts/Posts', [
        'posts' => $posts,
        'authUser' => auth()->user(), // Передаем данные текущего пользователя

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
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        // Ensure the user is authorized to edit this post
        if ($post->user_id !== Auth::id()) {
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
        // Ensure the user is authorized to delete this post
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the post
        $post->delete();

        // Redirect to the posts index page with a success message
        return redirect()->route('posts.index')->with('message', 'Post deleted successfully!');
    }
}
