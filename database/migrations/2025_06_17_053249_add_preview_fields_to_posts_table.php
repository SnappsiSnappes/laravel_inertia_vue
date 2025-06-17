<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreviewFieldsToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Добавляем поле для текста предпросмотра (максимум 1000 символов)
            $table->string('preview_text', 1000)->nullable();

            // Добавляем поле для ссылки на изображение предпросмотра (максимум 500 символов)
            $table->string('preview_image', 500)->nullable();
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Удаляем добавленные поля при откате миграции
            $table->dropColumn('preview_text');
            $table->dropColumn('preview_image');
        });
    }
}