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

// Функция для загрузки изображений и получения их размеров
const loadImagesWithSizes = async () => {
    if (!Array.isArray(props.files)) {
        console.warn("Files prop is not an array. Using empty array as fallback.");
        return;
    }

    const loadedImages = await Promise.all(
        props.files.map(async (file) => {
            const img = new Image();
            img.src = file.url;

            // Ждем загрузки изображения
            await new Promise((resolve, reject) => {
                img.onload = resolve;
                img.onerror = reject;
            });

            // Возвращаем данные изображения с реальными размерами
            return {
                ...file,
                width: img.naturalWidth, // Реальная ширина изображения
                height: img.naturalHeight, // Реальная высота изображения
            };
        })
    );

    // Обновляем реактивный массив
    imagesWithSizes.value = loadedImages;
};

// Инициализация PhotoSwipe
onMounted(async () => {
    await loadImagesWithSizes();

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
        <div id="gallery" class="gallery">
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
    margin: 20px 0;
}

.gallery {
    display: flex;
    gap: 10px;
    /* overflow-x: auto; */
    padding: 10px 0;
}

.gallery-item {
    flex: 0 0 auto;
    width: 200px;
    height: 200px;
    position: relative;
    cursor: pointer;

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