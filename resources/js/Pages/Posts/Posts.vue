<script setup>
import { Head } from '@inertiajs/vue3'
import PostCard from '../Components/PostCard.vue';

const props = defineProps({
    posts: Object,
    authUser: Object, // Добавляем пропс для данных текущего пользователя
    IsAdmin: Boolean
})

console.log(props.posts)

</script>

<template>

    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>All Posts</h1>

        <div class="mt-4">
            <a :href="route('posts.create')" class="primary-btn w-1/5">Create New Post</a>
        </div>

        <div v-for="post in posts.data" :key="post.id" class="mb-4 border-b pb-2">

            <PostCard :post="post" :IsAdmin="IsAdmin" :authUser="authUser"/>

        </div>

        <!-- Pagination -->
        <div v-if="posts.links.length > 3" class="mt-4">
            <Link v-for="(link, key) in posts.links" :key="key" :href="link.url" v-html="link.label"
                class="px-2 py-1 rounded" :class="{ 'bg-blue-500 text-white': link.active }" />
        </div>
    </div>
</template>

