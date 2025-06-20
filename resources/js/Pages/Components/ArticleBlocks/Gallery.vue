<script setup>
import { onMounted } from 'vue';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';

const props = defineProps({
    files: Array, // Массив изображений
    caption: String, // Подпись галереи
});

// Инициализация PhotoSwipe
onMounted(() => {
    const lightbox = new PhotoSwipeLightbox({
    gallery: '#gallery',
    children: 'a',
    pswpModule: () => import('photoswipe'),
    // showAnimationDuration: 333, // Длительность анимации открытия
    // hideAnimationDuration: 333, // Длительность анимации закрытия
        showHideAnimationType: 'none', // Отключаем анимацию открытия/закрытия
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
            <a v-for="(file, index) in files" :key="index" :href="file.url" :data-pswp-width="800"
                :data-pswp-height="600" class="gallery-item">
                <img :src="file.url" :alt="caption || 'Gallery image'" class="gallery-image" 
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
    overflow-x: auto;
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