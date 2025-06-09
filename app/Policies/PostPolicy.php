<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        // Разрешить просмотр списка постов только аутентифицированным пользователям
        return $user->id !== null;
    }

    /**
     * Determine whether the user can view a specific post.
     */
    public function view(User $user, Post $post): bool
    {

        // Разрешить просмотр поста всем пользователям (если нужно ограничить, измените логику)
        return true;
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        // Разрешить создание постов только аутентифицированным пользователям
        return $user->id !== null;
    }

    /**
     * Determine whether the user can update a specific post.
     */
    public function update(User $user, Post $post): bool
    {
        // Разрешить редактирование только владельцу поста
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete a specific post.
     */
    public function delete(User $user, Post $post): bool
    {
        // Разрешить удаление только владельцу поста
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can restore a deleted post.
     */
    public function restore(User $user, Post $post): bool
    {
        // Разрешить восстановление только владельцу поста
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can permanently delete a post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // Разрешить полное удаление только владельцу поста
        return $user->id === $post->user_id;
    }
}