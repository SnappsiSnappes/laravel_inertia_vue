<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    protected $fillable = [
        'post_id',
        'sticker_id',
        'user_id', // Убедитесь, что это поле присутствует
        'ip_address',
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с постом
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}