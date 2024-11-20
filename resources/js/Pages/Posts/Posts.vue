<script setup>


</script>

<template>

    <Head :title="`${$page.component}`" />

    <!-- #!! flash message -->
    <div v-if="$page.props.flash.message">
        <p class="p-4 bg-green-200">
            {{ $page.props.flash.message }}
        </p>
    </div>



    <div>
        posts
    </div>

    <div>
        {{ $page.props.city }}
    </div>


</template>