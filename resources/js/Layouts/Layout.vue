<script>
import { ref, computed, watch, onUnmounted } from 'vue';

export default {
    props: {
        auth: Object
    },
    setup(props) {
        const user = ref(props.auth?.user);

        const avatarSrc = computed(() => {
            return user.value?.avatar ? `storage/${user.value.avatar}` : 'storage/avatars/upload_preview.svg';
        });

        let stopWatcher;
        if (user.value) {
            stopWatcher = watch(() => props.auth.user?.avatar, (newAvatar) => {
                if (user.value) {
                    user.value.avatar = newAvatar;
                }
            });
        }




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
                    <Link class="nav-link" :href="route('home')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Home' }">Home</Link>
                    <Link class="nav-link" :href="route('about')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'About' }">About</Link>
                </div>

                <div v-if="!$page.props.auth.user" class="flex gap-5">
                    <Link class="nav-link" :href="route('register')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Auth/Register' }">Register</Link>
                    <Link class="nav-link" :href="route('login')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Auth/Login' }">Login</Link>
                </div>

                <div v-else class="gap-3 flex ">
                    <Link :href="route('edit')">
                    <img class="avatar cursor-pointer" :src="avatarSrc">
                    </Link>
                    <Link class="nav-link" :href="route('dashboard')"
                        :class="{ ' bg-slate-500 font-semibold ': $page.component === 'Dashboard' }">Dashboard </Link>
                    <span class="nav-link">
                        {{ $page.props.auth.user.name }}
                    </span>
                    <Link class="nav-link" :href="route('logout')" method="post" as="button" type="button">Logout
                    </Link>
                </div>
            </nav>
        </header>

        <main>
            <slot />
        </main>
    </div>


</template>
