<script setup>
import { onMounted, ref } from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import "photoswipe/style.css";
import ImageHelper from "../../../Objects/ImageHelper";

const props = defineProps({
    ImageUrl: String, // URL изображения
    caption: String, // Подпись к изображению
    Width: Number,
    Height: Number,
});

// Реактивная переменная для хранения данных изображения
let ConvertedFile = ref({});

onMounted(async () => {
    try {
        // Проверяем, есть ли ширина и высота в props
        if (!props.Width || !props.Height) {
            // Получаем размеры изображения через ImageHelper
            const fileWithSizes = await ImageHelper.GetImageWithSizes(props.ImageUrl);
            ConvertedFile.value = {
                ...fileWithSizes,
                caption: props.caption,
                ImageUrl: props.ImageUrl,
            };
        } else {
            // Используем данные из props
            ConvertedFile.value = {
                width: props.Width,
                height: props.Height,
                caption: props.caption,
                ImageUrl: props.ImageUrl,
            };
        }

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
    } catch (error) {
        console.error("Error processing image:", error);
    }
});
</script>

<template>
    <div class="image">
        <!-- Изображение -->
        <div id="image" class="image-div">
            <a
                :data-pswp-width="ConvertedFile.width"
                :data-pswp-height="ConvertedFile.height"
                :href="ConvertedFile.ImageUrl"
                class="image-item"
                target="_blank"
                rel="noreferrer"
            >
                <img
                    :src="ConvertedFile.ImageUrl"
                    :alt="ConvertedFile.caption || 'Image'"
                    class="image-image"
                />
            </a>

            <!-- Подпись -->
            <p v-if="ConvertedFile.caption" class="text-sm text-black mt-2 text-center">
                {{ ConvertedFile.caption }}
            </p>
        </div>
    </div>
</template>

<style scoped>
/* Обертка для изображения */
.image {
    @apply flex justify-center items-center overflow-hidden max-w-full mx-auto my-4;
}

/* Контейнер для изображения */
.image-div {
    @apply w-full max-w-[35rem] min-w-[20rem] mx-auto overflow-hidden rounded-lg shadow-md;
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