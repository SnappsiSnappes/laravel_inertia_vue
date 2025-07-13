<script setup>
import { onMounted, ref } from "vue";
import ImageHelper from "../../Objects/ImageHelper";
import PhotoSwipeLightbox from "photoswipe/lightbox";

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    IsAdmin: {
        type: Boolean,
        default: false,
    },
    authUser: {
        type: Object,
        default: () => ({}),
    },
});

// Реактивная переменная для хранения данных изображения
let ConvertedFile = ref({});

onMounted(async () => {
    // Получаем размеры изображения
    ConvertedFile.value = await ImageHelper.GetImageWithSizes(props.post.preview_image);
    console.log(ConvertedFile.value); // Логируем результат для проверки

    // Инициализация PhotoSwipeLightbox
    const lightbox = new PhotoSwipeLightbox({
        gallery: "#image",
        children: "a",
        pswpModule: () => import("photoswipe"),
        showHideAnimationType: "zoom", // Анимация открытия/закрытия
        closeOnScroll: false, // Не закрывать при прокрутке страницы
        wheelToZoom: true, // Разрешить зум колесиком мыши
    });
    lightbox.init();
});
</script>

<template>
    <div class="post-card rounded-lg overflow-hidden transition duration-300">
        <!-- Основной контент -->
        <div class="relative h-[400px] bg-gray-800 rounded-lg overflow-hidden">
            <!-- Изображение превью (фон) -->
            <div
                class="absolute top-0 left-0 w-full h-full bg-cover bg-center"
                :style="{ backgroundImage: `url(${post.preview_image})` }"
            >
                <!-- Затемнение снизу вверх -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
            </div>

            <!-- Кликабельное изображение для PhotoSwipeLightbox -->
            <div id="image" class="absolute inset-0 flex items-center justify-center">
                <a
                    :href="ConvertedFile.url"
                    :data-pswp-width="ConvertedFile.width"
                    :data-pswp-height="ConvertedFile.height"
                    class="w-full h-full cursor-pointer"
                ></a>
            </div>

            <!-- Текстовая часть поверх изображения -->
            <div class="absolute bottom-0 left-0 w-full p-6">
                <!-- Заголовок -->
                <h1 class="gradient-header mb-2">{{ post.title }}</h1>

                <!-- Превью текст -->
                <p class="cool-text">{{ post.preview_text }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.post-card {
    max-width: 900px;
}

/* Стиль для заголовка */
.gradient-header {
    font-family: 'Roboto Condensed', sans-serif;
    font-size: 40px; /* Увеличенный размер */
    font-weight: 600;
    margin: 0;
    padding: 0;
    color: transparent;
    background: linear-gradient(to right, #ff8a00, #da1b60);
    -webkit-background-clip: text;
    background-clip: text;
    text-shadow: none;
    display: inline-block;
}

/* Стиль для текста превью */
.cool-text {
    font-family: 'Roboto', sans-serif;
    font-size: 18px; /* Увеличенный размер */
    line-height: 1.6;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Показывает только 3 строки */
    -webkit-box-orient: vertical;
    margin: 0;
    padding: 0;
    transition: color 0.3s ease;
}

/* Стиль для контейнера */
.relative {
    height: 400px; /* Увеличенная высота */
}

/* Стиль для фонового изображения */
.bg-cover {
    background-size: cover;
    background-position: center;
}
</style>