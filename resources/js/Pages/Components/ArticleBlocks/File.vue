<script setup>
import { computed } from 'vue';
import { PaperClipIcon } from '@heroicons/vue/24/outline';
import { DocumentIcon } from '@heroicons/vue/24/outline';
import { PhotoIcon } from '@heroicons/vue/24/outline';
import TorrentSvg from "../../../svg/TorrentSvg.vue"
import { GifIcon } from '@heroicons/vue/16/solid';

const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
});

// Форматирование размера файла
const fileSize = computed(() => {
    const sizeInBytes = props.file.size;
    if (sizeInBytes < 1024) return `${sizeInBytes} B`;
    if (sizeInBytes < 1024 * 1024) return `${(sizeInBytes / 1024).toFixed(1)} KB`;
    return `${(sizeInBytes / (1024 * 1024)).toFixed(1)} MB`;
});

// Определение иконки на основе расширения файла
const fileIcon = computed(() => {
    const extension = props.file.extension.toLowerCase();
    switch (extension) {
        case 'jpg':
            return PhotoIcon; // Иконка для изображений

        case 'jpeg':
            return PhotoIcon; // Иконка для изображений

        case 'png':
            return PhotoIcon; // Иконка для изображений

        case 'gif':
            return GifIcon; // Иконка для изображений

        case 'bmp':
            return PhotoIcon; // Иконка для изображений

        case 'webp':
            return PhotoIcon; // Иконка для изображений
        case 'torrent':
            return TorrentSvg;
        case 'pdf':
            return DocumentIcon; // Иконка для PDF
        default:
            return PaperClipIcon; // Иконка по умолчанию
    }
});
</script>

<template>
    <div
        class="flex flex-row items-center justify-between p-4 border border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition duration-200">
        <!-- Иконка файла -->
        <div class="mr-4">
            <component :is="fileIcon" class="size-[2rem]" />
        </div>

        <!-- Информация о файле -->
        <div class="file-info flex-grow">
            <!-- Название файла -->
            <div class="file-name text-sm font-medium text-gray-800 break-all">
                {{ file.name }}
            </div>

            <!-- Размер файла -->
            <div class="file-size text-xs text-gray-500">
                {{ fileSize }}
            </div>
        </div>

        <!-- Кнопка скачивания -->
        <a :href="file.url" target="" download
            class="py-2 px-4 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 transition duration-200">
            Download
        </a>
    </div>
</template>

<style scoped></style>