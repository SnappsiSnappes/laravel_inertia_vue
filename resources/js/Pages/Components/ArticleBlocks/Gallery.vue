<script setup>
import { onMounted, ref } from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import "photoswipe/style.css";

const props = defineProps({
    files: Array, // Массив изображений
    caption: String, // Подпись галереи
});

// Реактивный массив для хранения данных изображений с размерами
const imagesWithSizes = ref([]);

onMounted(() => {
    if (!Array.isArray(props.files)) {
        console.warn("Files prop is not an array. Using empty array as fallback.");
        imagesWithSizes.value = [];
        return;
    }

    // Используем данные напрямую из props.files
    imagesWithSizes.value = props.files.map((file) => ({
        ...file,
        width: file.width || 800, // Если ширина не указана, используем значение по умолчанию
        height: file.height || 600, // Если высота не указана, используем значение по умолчанию
    }));

    const lightbox = new PhotoSwipeLightbox({
        gallery: "#gallery",
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
    <div class="gallery-div">
        <!-- Галерея -->
        <div id="gallery" class="gallery-main-div">
            <a
                v-for="(file, index) in imagesWithSizes"
                :key="index"
                :href="file.url"
                :data-pswp-width="file.width"
                :data-pswp-height="file.height"
                class="gallery-item"
                target="_blank"
                rel="noreferrer"
            >
                <img
                    :src="file.url"
                    :alt="caption || 'Gallery image'"
                    class="gallery-image"
                />
            </a>
        </div>

        <!-- Подпись -->
        <p v-if="caption" class="text-sm text-gray-500 mt-2 text-center">
            {{ caption }}
        </p>
    </div>
</template>

<style scoped>
.gallery-div {
    @apply w-full max-w-[50rem] mx-auto rounded-lg overflow-hidden;
    /* Максимальная ширина контейнера (800px = 50rem) */
}

.gallery-main-div {
    display: flex;
    flex-wrap: wrap;
    /* Разрешаем перенос строк */
    gap: 1rem;
    /* Устанавливаем расстояние между элементами */
    padding: 1rem;
    /* Добавляем внутренний отступ */
    justify-content: center;
    /* Центрируем элементы */
}

.gallery-item {
    flex: 0 0 auto;
    width: calc(33% - 2rem);
    /* Каждое изображение занимает ~33% ширины контейнера */
    height: 200px;
    /* Фиксированная высота */
    position: relative;
    cursor: pointer;

    @media (max-width: 768px) {
        width: calc(50% - 1rem);
        /* На маленьких экранах — 2 изображения в ряд */
    }

    @media (max-width: 480px) {
        width: 100%;
        /* На очень маленьких экранах — 1 изображение в ряд */
    }

    .gallery-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;

        &:hover {
            transform: scale(1.05);
        }
    }
}
</style>