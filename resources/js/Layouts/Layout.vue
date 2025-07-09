<script setup>
import { ref, computed, watch } from 'vue';
import FlashMessage from '../Pages/Components/FlashMessage.vue';
import { usePage } from '@inertiajs/vue3'; // Импортируем usePage
import Footer from './Footer.vue';

const page = usePage();


const props = defineProps({
    auth: Object,
});

const user = ref(props.auth?.user);

watch(
    () => props.auth.user,
    (newUser) => {
        user.value = newUser;
    },
    { immediate: true }
);

const avatarSrc = computed(() => {
    if (user.value && user.value.avatar) {
        return `/storage/${user.value.avatar}`;
    }
    return '/storage/avatars/upload_preview.png';
});

// Метод для очистки флеш-сообщения
function clearFlashMessage() {
    page.props.flash.message = null;
    page.props.flash.type = null;
};
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <header class="bg-zinc-700 text-white">
            <nav class="flex items-center justify-between p-4 max-w-screen-lg">
                <div class="space-x-6">
                    <Link class="nav-link" :href="route('home')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'Home' }">Home</Link>
                    <Link class="nav-link" :href="route('about')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'About' }">About</Link>
                    <Link class="nav-link" :href="route('posts.index')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'Posts/Posts' }">Posts</Link>
                    <Link class="nav-link" :href="route('all_users')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'AllUsers' }">All users</Link>
                </div>

                <div v-if="!page.props.auth.user" class="flex gap-5">
                    <Link class="nav-link" :href="route('register')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'Auth/Register' }">Register</Link>
                    <Link class="nav-link" :href="route('login')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'Auth/Login' }">Login</Link>
                </div>

                <div v-else class="gap-3 flex">
                    <span class="text-slate-200 rounded-md px-3 py-2 text-sm font-medium">
                        {{ page.props.auth.user.name }}
                    </span>
                    <img :src="avatarSrc" @error="event => event.target.src = '/storage/avatars/upload_preview.png'"
                        class="avatar">
                    <Link class="nav-link" :class="{ 'bg-slate-500 font-semibold': page.component === 'Auth/Edit' }"
                        :href="route('edit')">Edit profile</Link>
                    <Link class="nav-link" :href="route('dashboard')"
                        :class="{ 'bg-slate-500 font-semibold': page.component === 'Dashboard' }">Dashboard</Link>
                    <Link class="nav-link" :href="route('logout')" method="post" as="button" type="button">Logout</Link>
                </div>
            </nav>
        </header>
        <!-- Всплывающее уведомление -->
        <FlashMessage v-if="page.props.flash.message" :message="page.props.flash.message" :type="page.props.flash.type"
            @close="clearFlashMessage" />

        <main class="flex-grow py-8">
            <slot />
        </main>
        <div class="pt-52">
        <Footer />
    </div>
    </div>

</template>