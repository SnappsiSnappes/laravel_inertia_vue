<script setup>
import PaginationLinks from "./../Components/PaginationLinks.vue";

const props = defineProps({
    posts: Object,
})
console.log(props);
</script>

<template>
    <!-- head  -->
    <Head :title="`${$page.component}`" />


    <!-- container -->
    <div class="flex min-h-screen">

        <!-- #!! flash message -->
        <div v-if="$page.props.flash.message">
            <p class="p-4 bg-green-200">
                {{ $page.props.flash.message }}
            </p>
        </div>


        <!-- sidebar -->
        <div class="w-1/7 bg-gray-800 text-white p-4 space-y-4">
            <div class="p-1 border-2 mt-6 ">
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Create post</a>
            </div>
            <div>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">My posts</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Explore</a>
            </div>
        </div>

        <!-- content -->
        <div class="flex-1 p-4 bg-gray-100">
            <div v-for="post in posts.data" :key="post.id" class="mb-4">
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-center font-bold py-4">{{ post.title }}</div>
                    <div class="text-gray-600">
                        <i>Post by user: {{ post.user.name }} <br> Created at: {{ post.humanReadableDate }}</i>
                    </div>
                    <p>{{ post.short_body }}</p>
                </div>
                <hr class="my-4">
            </div>

            <div>
                <PaginationLinks :paginator="posts" />
            </div>

            <div>
                {{ $page.props.city }}
            </div>


        </div>
    </div>





</template>