<script setup></script>

<template>

    <Head :title="`${$page.component}`" />

    <p v-if="$page.props.flash.message" class="p-4 bg-green-200">
        {{ $page.props.flash.message }}
    </p>
    <h1 class="title">Welcome back {{ $page.props.auth.user.name }}</h1>
    <img src="/storage/app/public/dashboard.jpg" alt="asd">
</template>
