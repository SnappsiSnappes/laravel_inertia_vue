<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CleanupUnusedImages;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('cleanup:unused-images', function () {
    $this->call(CleanupUnusedImages::class);
})->purpose('Удаление неиспользуемых изображений');