<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    posts: Object,
})
</script>

<template>
    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>All Posts</h1>

        <div v-for="post in posts.data" :key="post.id" class="mb-4 border-b pb-2">
            <h2 class="text-lg font-bold">{{ post.title }}</h2>
            <p>{{ post.short_body }}...</p>
            <p class="text-sm text-gray-500">{{ post.humanReadableDate }}</p>

            <div class="flex gap-2 mt-2">
                <a :href="route('posts.show', post.id)" class="text-blue-500">View</a>
                <a :href="route('posts.edit', post.id)" class="text-green-500">Edit</a>
                <form @submit.prevent="deletePost(post.id)">
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a :href="route('posts.create')" class="primary-btn">Create New Post</a>
        </div>

        <!-- Pagination -->
        <div v-if="posts.links.length > 3" class="mt-4">
            <Link
                v-for="(link, key) in posts.links"
                :key="key"
                :href="link.url"
                v-html="link.label"
                class="px-2 py-1 rounded"
                :class="{ 'bg-blue-500 text-white': link.active }"
            />
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