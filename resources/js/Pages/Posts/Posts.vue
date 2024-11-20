<script setup>
import PaginationLinks from "./../Components/PaginationLinks.vue";

const props = defineProps({
    posts: Object,
})
console.log(props);
</script>

<template>

    <Head :title="`${$page.component}`" />

    <!-- #!! flash message -->
    <div v-if="$page.props.flash.message">
        <p class="p-4 bg-green-200">
            {{ $page.props.flash.message }}
        </p>
    </div>

    <div v-for="post in posts.data" :key="post.id">
        <div>
            <div class="text-center font-bold py-4"> {{ post.title }} </div>
            <p>{{ post.short_body }}</p>
            <div class="">
                <i>post by user : {{ post.user.name }} <br> created at {{ post.humanReadableDate }}</i>
            </div>
        </div>

        <hr style=" height:2px; background-color:gray; border:none;">

    </div>

    <div>
        <PaginationLinks :paginator="posts" />
    </div>

    <div>
        {{ $page.props.city }}
    </div>


</template>