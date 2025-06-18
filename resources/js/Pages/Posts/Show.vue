<script setup>
import { Head } from '@inertiajs/vue3'
import PostContent from '../Components/PostContent.vue'
import { router } from '@inertiajs/vue3';

const props = defineProps({
    post: Object,
    IsAdmin: Boolean,
    authUser: Object,
})


</script>

<template>

    <Head :title="`${post.title}`" />
    <div class="w-2/4 mx-auto">
        <div class="mt-4">
            <a :href="route('posts.index')" class="text-blue-500">Back to posts</a>
        </div>

        <!-- Проверка на авторизацию и владение постом -->
        <div class="flex gap-2 mt-2" v-if="(authUser && authUser.id === post.user.id) || IsAdmin">
            <a :href="route('posts.edit', post.id)" class="text-blue-500">Edit</a>
            <form @submit.prevent="deletePost(post.id)">
                <button type="submit" class="text-red-500">Delete</button>
            </form>
        </div>

        <!-- Используем компонент PostContent -->
        <PostContent :post="post" :IsAdmin="IsAdmin" :authUser="authUser" />


    </div>
</template>

<script>
export default {
    methods: {
        deletePost(id) {
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
        },
    },
};
</script>