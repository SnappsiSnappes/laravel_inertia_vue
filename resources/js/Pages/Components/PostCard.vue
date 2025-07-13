<template>
    <div class="post-card rounded-lg overflow-hidden transition duration-300">
        <!-- Основной контент -->
        <div class="relative pt-6 flex flex-col md:flex-row gap-1">
            <!-- Изображение превью -->
            <div class="preview-image max-w-[23.25rem] h-64 w-full">
                <img :src="post.preview_image" alt="Post Preview" class="w-full h-full object-cover rounded-lg" />
            </div>

            <!-- Текстовая часть -->
            <div class="flex flex-col flex-grow justify-between content-container">
                <!-- Заголовок и метаданные -->
                <div class="text-content">
                    <a :href="route('posts.show', post.id)">
                        <h1 class="cool-header mb-2">{{ post.title }}</h1>
                    </a>
                    <!-- Добавлен preview_text -->
                    <p class="cool-text">{{ post.preview_text }}</p>
                </div>

                <!-- Действия (View, Edit, Delete) -->
                <div class="actions flex flex-col gap-2 mt-4">
                    <div class="flex gap-2">
                        <!-- Кнопка "View" -->
                        <a :href="route('posts.show', post.id)" class="text-blue-500 hover:text-blue-700">View</a>

                        <!-- Проверка на авторизацию и владение постом -->
                        <template v-if="(authUser && authUser.id === post.user_id) || IsAdmin">
                            <a :href="route('posts.edit', post.id)"
                                class="text-yellow-500 hover:text-yellow-700">Edit</a>
                            <form @submit.prevent="deletePost(post.id)">
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
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

<script setup>
import { router } from "@inertiajs/vue3";

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

// Метод для удаления поста
const deletePost = (id) => {
    if (confirm("Are you sure you want to delete this post?")) {
        router.delete(route("posts.destroy", id), {
            onSuccess: () => {
                console.log("Post deleted successfully");
            },
            onError: () => {
                console.error("Error deleting post");
            },
        });
    }
};
</script>

<style scoped>
.post-card {
    max-width: 900px;
}

/* Стиль для заголовка */
.cool-header {
    font-family: 'Roboto', sans-serif;
    font-size: 2rem;
    font-weight: bold;
    color: transparent;
    background: linear-gradient(90deg, #FF7F50, #FF6347);
    -webkit-background-clip: text;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    position: relative;
    display: inline-block;
}



/* Стиль для текста превью */
.cool-text {
    font-family: 'Roboto', sans-serif;
    font-size: 15px;
    line-height: 1.6;
    color: rgb(136, 145, 157);
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    /* Показывает только 3 строки */
    -webkit-box-orient: vertical;
    margin: 12px 0 0;
    padding: 0;
    transition: color 0.3s ease;
}



/* Стиль для контейнера справа */
.content-container {
    display: table-cell;
    vertical-align: top;
    background-color: rgb(8, 12, 15);
    min-height: 250px;
    width: 100%;
    font-size: 16px;
    position: relative;
    border-radius: 0px 3px 3px 0px;
    padding: 15px 22px;
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