<script setup>
const props = defineProps({
    posts: Object,
})

</script>

<template>

    <Head :title="`${$page.component}`" />

    <!-- #!! flash message -->
    <div v-if="$page.props.flash.message">
        <p class="p-4 bg-green-200">
            {{ $page.props.flash.message }}
        </p>
    </div>

    <div v-for="post in posts">
        <div>
            <div class="text-center font-bold py-4"> {{ post.title }} </div>
            <p>{{ post.short_body }}</p>
            <div class="">
                <i>post by user: {{ post.user.name }} <br> created at {{ post.humanReadableDate }}</i>
            </div>
        </div>

        <hr style=" height:2px; background-color:gray; border:none;">

    </div>

    <div>
        posts
    </div>

    <div>
        {{ $page.props.city }}
    </div>


</template>