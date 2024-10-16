<script setup></script>

<template>
    <Head :title="`${$page.component}`" />

    <p v-if="$page.props.flash.message" class="p-4 bg-green-200">
        {{ $page.props.flash.message }}
    </p>
    <div v-if="$page.props.auth.user">
        <h1 class="title">Welcome back {{ $page.props.auth.user.name }}</h1>
    </div>
</template>
