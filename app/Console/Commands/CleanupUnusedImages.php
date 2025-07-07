<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class CleanupUnusedImages extends Command
{
    protected $signature = 'cleanup:unused-images';
    protected $description = 'Удаление неиспользуемых изображений из storage/app/public/images';

    public function handle()
    {
        // 1. Получаем список всех изображений в папке storage/app/public/images
        $allImages = Storage::disk('public')->files('images');

        // 2. Извлекаем URL всех изображений, используемых в постах
        $usedImagesInBody = Post::pluck('body')
            ->map(function ($body) {
                $data = json_decode($body, true);
                $blocks = $data['blocks'] ?? [];
                return collect($blocks)
                    ->filter(fn ($block) => $block['type'] === 'image')
                    ->pluck('data.file.url')
                    ->toArray();
            })
            ->flatten()
            ->map(function ($url) {
                // Преобразуем полный URL в относительный путь без расширения
                return $this->normalizePath($url);
            })
            ->unique();

        // 3. Извлекаем URL всех preview_image
        $usedPreviewImages = Post::pluck('preview_image')
            ->filter() // Убираем null или пустые значения
            ->map(function ($url) {
                // Преобразуем полный URL в относительный путь без расширения
                return $this->normalizePath($url);
            })
            ->toArray();

        // 4. Объединяем все используемые изображения
        $usedImages = array_unique(array_merge($usedImagesInBody->toArray(), $usedPreviewImages));

        // 5. Находим файлы, которые больше не используются
        $unusedImages = array_filter($allImages, function ($file) use ($usedImages) {
            // Преобразуем путь к файлу в нормализованный формат
            $normalizedFile = $this->normalizePath($file);
            return !in_array($normalizedFile, $usedImages);
        });

        // print_r([
        //     '$usedImages' => $usedImages,
        //     '$unusedImages' => $unusedImages
        // ]);

        // 6. Удаляем неиспользуемые файлы
        foreach ($unusedImages as $file) {
            Storage::disk('public')->delete($file);
            $this->info("Deleted: {$file}");
        }

        $this->info('Cleanup completed.');
    }

    /**
     * Нормализует путь к файлу: удаляет /storage/, преобразует в нижний регистр и убирает расширение.
     */
    private function normalizePath($path)
    {
        // Удаляем /storage/ из пути
        $normalized = str_replace('/storage/', '', $path);

        // Убираем расширение файла
        $normalized = pathinfo($normalized, PATHINFO_FILENAME);

        // Преобразуем в нижний регистр для унификации
        return strtolower($normalized);
    }
}