<script setup>
import { computed } from 'vue';
import { PaperClipIcon } from '@heroicons/vue/16/solid';


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

// Проверка, является ли файл изображением
const isImage = computed(() => {
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    return imageExtensions.includes(props.file.extension.toLowerCase());
});
</script>

<template>
    <div class=" flex items-center p-4 border border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition duration-200">
        <!-- Иконка файла -->
        <div class=" mr-4">
            <svg v-if="isImage" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <svg v-else-if="file.extension === 'pdf'" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <PaperClipIcon v-else  class="size-[2rem]" />
        </div>

        <!-- Информация о файле -->
        <div class="file-info flex-grow">
            <!-- Название файла -->
            <div class="file-name text-sm font-medium text-gray-800 truncate">
                {{ file.name }}
            </div>

            <!-- Размер файла -->
            <div class="file-size text-xs text-gray-500">
                {{ fileSize }}
            </div>
        </div>

        <!-- Кнопка скачивания -->
        <a :href="file.url" target="" class=" py-2 px-4 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 transition duration-200">
            Download
        </a>
    </div>
</template>



<style scoped>



</style>