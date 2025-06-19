<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import TextInput from '../Components/TextInput.vue';
import Editor from '../Components/EditorJs.vue'; // Импортируем новый компонент

const props = defineProps({
    post: Object,
});

// Инициализация формы
const form = useForm({
    title: props.post.title || '',
    body: props.post.body || '',
    preview_text: props.post.preview_text || '',
    preview_image: props.post.preview_image, // Файл изображения
    preview_image_url: props.post.preview_image, // URL для предпросмотра

});


// Обработка сохранения
const handleSave = async (editorData) => {
    form.body = JSON.stringify(editorData);

    // Логируем данные перед отправкой
    console.log('Form data before submission:', form);

    // Отправляем форму через POST
    form.post(route('posts.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Post updated successfully');
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
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
        <h1>Edit Post</h1>

        <form @submit.prevent="$refs.editor.save()">


            <!-- Поле для preview_text -->
            <TextInput name="Preview Text" v-model="form.preview_text" :message="form.errors.preview_text" />

            <!-- Поле для загрузки изображения -->
            <label>Preview Image</label>
            <img v-if="form.preview_image_url" :src="form.preview_image_url" class="object-cover w-28 h-28">
            <input type="file" @input="AddFile" name="preview_image" class="mt-2">
            <small v-if="form.errors.preview_image" class="error">{{ form.errors.preview_image }}</small>


            <!-- Поле для заголовка -->
            <TextInput name="Title" v-model="form.title" :message="form.errors.title" />

            <!-- Компонент Editor -->
            <Editor ref="editor" :initialData="JSON.parse(post.body)" @save="handleSave" />

            <!-- Простое текстовое поле для отображения ошибок -->
            <div v-if="form.errors.body" class="text-red-500 mt-2">{{ form.errors.body }}</div>

            <!-- Кнопка отправки формы -->
            <div class="mt-4">
                <button class="primary-btn" :disabled="form.processing">
                    Update Post
                </button>
            </div>
        </form>
    </div>
</template>