<script setup>


</script>

<template>

    <Head :title="`${$page.component}`" />

    <!-- #!! flash message -->
    <div>
        <p v-if="$page.props.flash.message" class="p-4 bg-green-200">
            {{ $page.props.flash.message }}
        </p>
    </div>

    <div>
        posts
    </div>



</template>