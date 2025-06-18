<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostReactionsTable extends Migration
{
    public function up()
    {
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // Связь с постом
            $table->string('sticker_id'); // ID стикера или гифки
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID пользователя
            $table->string('ip_address')->nullable(); // IP-адрес (опциональное поле)
            $table->timestamps();

            // Уникальный индекс для предотвращения дублирования реакций
            $table->unique(['post_id', 'sticker_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_reactions');
    }
}