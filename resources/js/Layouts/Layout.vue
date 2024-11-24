<script>
import { ref, computed, watch } from 'vue';

export default {
    props: {
        auth: Object
    },
    setup(props) {
        const user = ref(props.auth?.user);

        // Watch for user changes and get user properties
        watch(
            () => props.auth.user,
            (newUser) => {
                user.value = newUser;
            },
            { immediate: true }
        );

        // Look for avatar and place it, place default if there is no one
        const avatarSrc = computed(() => {
            if (user.value && user.value.avatar) {
                return `/storage/${user.value.avatar}`;
            }
            return '/storage/avatars/upload_preview.png';
        });

        return {
            avatarSrc
        };
    }
};
</script>

<template>
    <div>
        <header class="bg-zinc-700 text-white">
            <nav class="flex items-center justify-between p-4 max-w-screen-lg">
                <div class="space-x-6">
                    <!-- Home -->
                    <Link class="nav-link" :href="route('home')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Home' }">Home</Link>
                    <!-- About -->
                    <Link class="nav-link"  :href="route('about')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'About' }">About</Link>
                    <!-- Posts -->
                    <Link class="nav-link"  :href="route('posts.index')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Posts/Posts' }">Posts</Link>
                    <!-- All users -->
                    <Link class="nav-link"  :href="route('all_users')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'AllUsers' }">All users</Link>
                </div>

                <div v-if="!$page.props.auth.user" class="flex gap-5">
                    <!-- Register -->
                    <Link class="nav-link" :href="route('register')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Auth/Register' }">Register</Link>
                    <!-- Login -->
                    <Link class="nav-link" :href="route('login')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Auth/Login' }">Login</Link>
                </div>

                <div v-else class="gap-3 flex">
                    <!-- Name of user -->
                    <span class="text-slate-200 rounded-md px-3 py-2 text-sm font-medium">
                        {{ $page.props.auth.user.name }}
                    </span>

                    <!-- Avatar with fallback -->
                    <img :src="avatarSrc" @error="event => event.target.src = '/storage/avatars/upload_preview.png'" class="avatar">

                    <Link class="nav-link" :class="{ 'bg-slate-500 font-semibold ': $page.component === 'Auth/Edit' }" :href="route('edit')">
                        Edit profile
                    </Link>

                    <!-- Dashboard -->
                    <Link class="nav-link" :href="route('dashboard')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Dashboard' }">Dashboard</Link>

                    <!-- Logout -->
                    <Link class="nav-link" :href="route('logout')" method="post" as="button" type="button">Logout</Link>
                </div>
            </nav>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
