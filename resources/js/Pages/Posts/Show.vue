<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    post: Object,
})
</script>

<template>
    <Head :title="`${post.title}`" />
    <div class="w-2/4 mx-auto">
        <h1>{{ post.title }}</h1>
        <p>{{ post.body }}</p>
        <p class="text-sm text-gray-500">{{ post.humanReadableDate }}</p>

        <div class="mt-4">
            <a :href="route('posts.index')" class="text-blue-500">Back to posts</a>
        </div>
    </div>
</template>