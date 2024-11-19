<script setup>


</script>

<template>

    <Head :title="`${$page.component}`" />

    <div>
        home
    </div>

    <!-- flash message -->
    <p v-if="$page.props.flash.message" class="p-4 bg-green-200">
        {{ $page.props.flash.message }}
    </p>
    
</template>