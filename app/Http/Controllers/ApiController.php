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

class ApiController extends Controller
{
    public function StoreFile(Request $request)
    {
        // Проверяем, был ли загружен файл
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        // Получаем загруженный файл
        $file = $request->file('image');

        // Вычисляем хэш файла
        $hash = hash_file('sha256', $file->getRealPath());

        // Проверяем, существует ли файл с таким хэшом
        $existingFilePath = Storage::disk('public')->path("images/{$hash}");

        if (Storage::disk('public')->exists("images/{$hash}")) {
            // Если файл уже существует, возвращаем его URL
            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => Storage::disk('public')->url("images/{$hash}"),
                    'width' => null,
                    'height' => null,
                ],
            ]);
        }

        // Если файла нет, сохраняем его с именем, равным хэшу
        $path = "images/{$hash}";
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        // Получаем размеры изображения
        $dimensions = getimagesize($file->getRealPath());
        $width = $dimensions[0];
        $height = $dimensions[1];

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => Storage::disk('public')->url($path),
                'width' => $width,
                'height' => $height,
            ],
        ]);
    }

    public function deleteImages(Request $request)
    {
        $urls = $request->input('urls', []);

        foreach ($urls as $url) {
            // Извлекаем путь к файлу из URL
            $path = parse_url($url, PHP_URL_PATH);
            $relativePath = str_replace('/storage/', '', $path);

            // Удаляем файл
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        return response()->json(['success' => true]);
    }
}