<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { PostCardHelper } from '../../Objects/PostCardHelper';
import ToggleReaction from '../Posts/ToggleReaction.vue';

const props = defineProps({
    post: Object,
    IsAdmin: Boolean,
    authUser: Object,
});


// Метод для удаления поста
const deletePost = (id) => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('posts.destroy', id), {
            onSuccess: () => {
                console.log('Post deleted successfully');
            },
            onError: () => {
                console.error('Error deleting post');
            },
        });
    }
};
</script>

<template>
    <div>
        <h1 class="header-1">{{ post.title }}</h1>

        <p class="text-sm text-gray-500">created {{ post.humanReadableDate }}</p>
        <p class="text-sm text-gray-500">by user {{ post.user.email }}</p>

        <div class="flex gap-2 mt-2">
            <!-- Кнопка "View" -->
            <a :href="route('posts.show', post.id)" class="text-blue-500">View</a>

            <!-- Проверка на авторизацию и владение постом -->
            <template v-if="(authUser && authUser.id === post.user_id) || IsAdmin">
                <a :href="route('posts.edit', post.id)" class="text-blue-500">Edit</a>
                <form @submit.prevent="deletePost(post.id)">
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </template>
        </div>

        <div class="PreviewDiv">
            <p>{{ post.preview_text }}</p>

            <div v-if="post.preview_image" class="image">
                <img width="200px" :src="post.preview_image" />
            </div>
        </div>
        
        <ToggleReaction :PostId="post.id" />

    </div>
</template>

