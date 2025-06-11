<script setup>
import { Head } from '@inertiajs/vue3'
import PostContent from '../Components/PostContent.vue'

const props = defineProps({
    posts: Object,
    authUser: Object, // Добавляем пропс для данных текущего пользователя
    IsAdmin: Boolean
})

console.log(props.IsAdmin)
</script>

<template>
    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>All Posts</h1>

        <div v-for="post in posts.data" :key="post.id" class="mb-4 border-b pb-2">
            <h2 class="text-lg font-bold">{{ post.title }}</h2>
            <PostContent :post="post" />
            <p class="text-sm text-gray-500">{{ post.humanReadableDate }}</p>

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
        </div>

        <div class="mt-4">
            <a :href="route('posts.create')" class="primary-btn">Create New Post</a>
        </div>

        <!-- Pagination -->
        <div v-if="posts.links.length > 3" class="mt-4">
            <Link v-for="(link, key) in posts.links" :key="key" :href="link.url" v-html="link.label"
                class="px-2 py-1 rounded" :class="{ 'bg-blue-500 text-white': link.active }" />
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3'

export default {
    methods: {
        deletePost(id) {
            if (confirm('Are you sure you want to delete this post?')) {
                router.delete(route('posts.destroy', id), {
                    onSuccess: () => {
                        console.log('Post deleted successfully')
                    },
                    onError: () => {
                        console.error('Error deleting post')
                    }
                })
            }
        }
    }
}
</script>