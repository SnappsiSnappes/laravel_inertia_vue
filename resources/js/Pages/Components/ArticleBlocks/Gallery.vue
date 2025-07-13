<script setup>
import { onMounted, ref } from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import "photoswipe/style.css";
import ImageHelper from "../../../Objects/ImageHelper";

const props = defineProps({
    files: Array, // Массив изображений
    caption: String, // Подпись галереи
});

let ConvertedFiles = ref([]);

onMounted(async () => {
    if (!Array.isArray(props.files) || props.files.length === 0) {
        console.warn("Files prop is not an array or is empty. Using empty array as fallback.");
        return;
    }

    // Преобразуем массив файлов
    ConvertedFiles.value = await Promise.all(
        props.files.map(async (file) => {
            if (!file.width || !file.height) {
                // Если ширина или высота отсутствуют, получаем их через ImageHelper
                const updatedFile = await ImageHelper.GetImageWithSizes(file.url);
                return updatedFile;
            }
            return file; // Иначе возвращаем оригинальный файл
        })
    );

    // Инициализируем PhotoSwipeLightbox
    const lightbox = new PhotoSwipeLightbox({
        gallery: "#gallery",
        children: "a",
        pswpModule: () => import("photoswipe"),
        showHideAnimationType: "zoom", // Анимация открытия/закрытия
        closeOnScroll: false, // Не закрывать при прокрутке страницы
        wheelToZoom: true, // Разрешить зум колесиком мыши
    });
    lightbox.init();

    console.log(ConvertedFiles.value); // Логируем результат для проверки
});
</script>

<template>
    <div class="gallery-div">
        <!-- Галерея -->
        <div id="gallery" class="gallery-main-div">
            <a
                v-for="(file, index) in ConvertedFiles"
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
}

.gallery-main-div {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1rem;
    justify-content: center;
}

.gallery-item {
    flex: 0 0 auto;
    width: calc(33% - 2rem);
    height: 200px;
    position: relative;
    cursor: pointer;

    @media (max-width: 768px) {
        width: calc(50% - 1rem);
    }

    @media (max-width: 480px) {
        width: 100%;
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