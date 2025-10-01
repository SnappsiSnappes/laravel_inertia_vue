<!-- PostCard.vue -->
<script setup>
import { router } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import ImageHelper from "../../Objects/ImageHelper";

const props = defineProps({
    post: { type: Object, required: true },
    IsAdmin: { type: Boolean, default: false },
    authUser: { type: Object, default: () => ({}) },
});

const ConvertedFile = ref(null);

onMounted(async () => {
    if (props.post.preview_image) {
        ConvertedFile.value = await ImageHelper.GetImageWithSizes(props.post.preview_image);

        const lightbox = new PhotoSwipeLightbox({
            gallery: "#image",
            children: "a",
            pswpModule: () => import("photoswipe"),
            showHideAnimationType: "zoom",
            closeOnScroll: false,
            wheelToZoom: true,
        });
        lightbox.init();
    }
});

const deletePost = (id) => {
    if (confirm("Are you sure you want to delete this post?")) {
        router.delete(route("posts.destroy", id));
    }
};
</script>

<template>
  <div class="post-card rounded-lg overflow-hidden transition duration-300">
    <!-- Grid-контейнер -->
    <div class="post-grid">
      <!-- Изображение превью -->
      <div id="image" class="preview-image">
        <template v-if="ConvertedFile">
          <a
            :data-pswp-width="ConvertedFile.width"
            :data-pswp-height="ConvertedFile.height"
            :href="ConvertedFile.url"
            class="image-link"
          >
            <img
              :src="ConvertedFile.url"
              :alt="ConvertedFile.caption || 'Image'"
              class="preview-img"
            />
          </a>
        </template>
        <template v-else>
          <div class="image-placeholder">
            <span class="text-gray-500">No image</span>
          </div>
        </template>
      </div>

      <!-- Текстовая часть -->
      <div class="content-container">
        <div class="text-content">
          <a :href="route('posts.show', post.id)">
            <h1 class="cool-header mb-2">{{ post.title }}</h1>
          </a>
          <p class="cool-text">{{ post.preview_text }}</p>
        </div>

        <div class="actions flex flex-col gap-2 mt-4">
          <div class="flex gap-2 flex-wrap">
            <a :href="route('posts.show', post.id)" class="text-blue-500 hover:text-blue-700">View</a>
            <template v-if="(authUser && authUser.id === post.user_id) || IsAdmin">
              <a :href="route('posts.edit', post.id)" class="text-yellow-500 hover:text-yellow-700">Edit</a>
              <button @click="deletePost(post.id)" class="text-red-500 hover:text-red-700 bg-transparent border-0">
                Delete
              </button>
            </template>
          </div>
          <div class="meta text-xs text-gray-500">
            Published {{ post.humanReadableDate }} by {{ post.user.email }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.post-card {
  max-width: 900px;
}

/* Grid layout */
.post-grid {
  display: grid;
  grid-template-columns: 1fr;
  min-height: 250px;
}

/* На десктопе — две колонки */
@media (min-width: 768px) {
  .post-grid {
    grid-template-columns: 372px 1fr;
  }
}

/* Левая колонка — изображение */
.preview-image {
  background-color: #000;
  border-radius: 3px 0 0 3px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  position: relative;
}

.image-link {
  display: block;
  width: 100%;
  height: 100%;
}

.preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  min-height: 250px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #111;
}

/* Правая колонка — контент */
.content-container {
  background-color: rgb(8, 12, 15);
  padding: 15px 22px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-radius: 0 3px 3px 0;
}

/* Стили текста — без изменений */
.cool-header {
  font-family: 'Roboto', sans-serif;
  font-size: 2rem;
  font-weight: bold;
  color: transparent;
  background: linear-gradient(90deg, #FF7F50, #FF6347);
  -webkit-background-clip: text;
  background-clip: text;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  display: inline-block;
}

.cool-text {
  font-family: 'Roboto', sans-serif;
  font-size: 15px;
  line-height: 1.6;
  color: rgb(136, 145, 157);
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  margin: 12px 0 0;
  padding: 0;
}

.text-content {
  color: white;
}

.actions {
  color: white;
}

.meta {
  color: rgba(255, 255, 255, 0.7);
}
</style>