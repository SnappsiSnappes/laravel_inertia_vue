<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import Editor from '../Components/EditorJs.vue';

const form = useForm({
    title: null,
    body: null,
    preview_text: null,
    preview_image: null, // Файл изображения
    preview_image_url: null, // URL для предпросмотра
});

// Обработка сохранения данных из редактора
const handleSave = (editorData) => {
    form.body = JSON.stringify(editorData);
    form.post(route('posts.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Errors:', errors);
        },
    });
};

// Обработка загрузки изображения
const AddFile = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.preview_image = file; // Добавляем файл в форму
        form.preview_image_url = URL.createObjectURL(file); // Создаем URL для предпросмотра
    }
};
</script>

<template>
    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>Create a new post</h1>

        <form @submit.prevent="$refs.editor.save()">
            <!-- Поле для заголовка -->
            <TextInput name="Title" v-model="form.title" :message="form.errors.title" />

            <!-- Поле для preview_text -->
            <TextInput name="Preview Text" v-model="form.preview_text" :message="form.errors.preview_text" />

            <!-- Поле для загрузки изображения -->
            <label>Preview Image</label>
            <img v-if="form.preview_image_url" :src="form.preview_image_url" class="object-cover w-28 h-28">
            <input type="file" @input="AddFile" name="preview_image" class="mt-2">
            <small v-if="form.errors.preview_image" class="error">{{ form.errors.preview_image }}</small>

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

