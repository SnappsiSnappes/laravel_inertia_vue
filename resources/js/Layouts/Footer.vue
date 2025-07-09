<script setup>
</script>
<template>
    <!-- Footer -->
    <footer class="bg-zinc-800 text-white py-12">
        <div class="max-w-screen-lg mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <!-- Левая часть: Копирайт -->
                <div class="text-center md:text-left">
                    <p class="text-sm">&copy; 2023 YourAppName. All rights reserved.</p>
                </div>

                <!-- Центральная часть: Навигация -->
                <div class="space-x-6">
                    <Link class="nav-link" :href="route('home')">Home</Link>
                    <Link class="nav-link" :href="route('about')">About</Link>
                    <Link class="nav-link" :href="route('posts.index')">Posts</Link>
                    <Link class="nav-link" :href="route('all_users')">All users</Link>
                </div>

                <!-- Правая часть: Социальные сети -->
                <div class="flex gap-4">
                    <a href="#" class="text-white hover:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2v2m0 16v2m-4-9h4m-4 0V5m0 14v-2m4 0h4m-4 0V5m0 14v-2" />
                        </svg>
                    </a>
                    <a href="#" class="text-white hover:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4V14a6 6 0 016-6z" />
                        </svg>
                    </a>
                    <a href="#" class="text-white hover:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
