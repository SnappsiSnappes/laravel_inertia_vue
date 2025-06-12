<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import Editor from '../Components/EditorJs.vue'; // Импортируем новый компонент

const form = useForm({
    title: null,
    body: null,
});

const handleSave = (editorData) => {
    form.body = JSON.stringify(editorData);
    form.post(route('posts.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Errors:', errors);
        },
    });
};
</script>

<template>
    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>Create a new post</h1>

        <form @submit.prevent="$refs.editor.save()">
            <!-- Поле для заголовка -->
            <TextInput name="Title" v-model="form.title" :message="form.errors.title" />

            <!-- Компонент Editor -->
            <Editor ref="editor" @save="handleSave" />

            <!-- Простое текстовое поле для отображения ошибок -->
            <div v-if="form.errors.body" class="text-red-500 mt-2">{{ form.errors.body }}</div>

            <!-- Кнопка отправки формы -->
            <div class="mt-4">
                <button class="primary-btn" :disabled="form.processing">
                    Create Post
                </button>
            </div>
        </form>
    </div>
</template>