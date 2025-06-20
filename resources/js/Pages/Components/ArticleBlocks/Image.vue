<script setup>
import { onMounted, ref } from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import "photoswipe/style.css";

const props = defineProps({
    ImageUrl: String, // Массив изображений
    caption: String, // Подпись галереи
});

// Реактивный массив для хранения данных изображений с размерами
const imagesWithSizes = ref([]);

// Функция для загрузки изображения и получения его размеров
const loadImageWithSizes = async () => {
    const img = new Image();
    img.src = props.ImageUrl;

    // Ждем загрузки изображения
    await new Promise((resolve, reject) => {
        img.onload = resolve;
        img.onerror = reject;
    });

    // Обновляем реактивный массив с данными изображения
    imagesWithSizes.value = [
        {
            url: props.ImageUrl,
            width: img.naturalWidth, // Реальная ширина изображения
            height: img.naturalHeight, // Реальная высота изображения
        },
    ];
};

// Инициализация PhotoSwipe
onMounted(async () => {
    await loadImageWithSizes();

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
    <div class="image ">
        <!-- Галерея -->
        <div id="image" class="image-div">
            <a v-for="(file, index) in imagesWithSizes" :key="index" :href="file.url" :data-pswp-width="file.width"
                :data-pswp-height="file.height" class="image-item" target="_blank" rel="noreferrer">
                <img :src="file.url" :alt="caption || 'image image'" class="image-image" />
            </a>
        </div>

        <!-- Подпись -->
        <p v-if="caption" class="text-sm text-gray-500 mt-2 text-center">
            {{ caption }}
        </p>
    </div>
</template>


<style scoped>
/* Обертка для изображения */
.image {
    @apply flex justify-center items-center overflow-hidden max-w-full mx-auto my-4;
}

/* Контейнер для изображения */
.image-div {
    @apply w-full max-w-[35rem] mx-auto overflow-hidden rounded-lg shadow-md; /* 800px = 50rem */
}

/* Ссылка с изображением */
.image-item {
    @apply block w-full h-auto cursor-pointer transition-transform duration-300 ease-in-out;
}

/* Само изображение */
.image-image {
    @apply w-full h-auto object-cover rounded-lg;
}

/* Добавляем стили для растянутых изображений */
.image.stretched {
    @apply w-full;
}

.image.stretched img {
    @apply w-full h-auto rounded-md object-cover;
}
</style>