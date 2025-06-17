<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        "title",
        "body",
        "user_id", // Добавьте user_id
        "preview_text", // Новое поле для текста предпросмотра
        "preview_image", // Новое поле для ссылки на изображение

    ];


    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
