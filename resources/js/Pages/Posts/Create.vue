<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue'
import EditorJS from '@editorjs/editorjs'
import Header from '@editorjs/header' // Заголовки
import List from '@editorjs/list' // Списки
import Quote from '@editorjs/quote' // Цитаты
import ImageTool from '@editorjs/image' // Изображения
import CodeTool from '@editorjs/code' // Код
import Table from '@editorjs/table' // Таблицы
import Delimiter from '@editorjs/delimiter' // Разделители
import Embed from '@editorjs/embed' // Встраиваемые элементы
import axios from 'axios';

onMounted(() => {
    editor.value = new EditorJS({
        holder: 'editorjs', // ID элемента, в котором будет размещен редактор
        tools: {
            header: Header, // Плагин для заголовков
            list: List, // Плагин для списков
            quote: Quote, // Плагин для цитат
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: '/upload-image', // URL для загрузки изображений
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': axios.defaults.headers.common['X-CSRF-TOKEN'],
                    },
                },
            }, // Плагин для изображений
            code: CodeTool, // Плагин для кода
            table: Table, // Плагин для таблиц
            delimiter: Delimiter, // Плагин для разделителей
            embed: Embed, // Плагин для встраиваемых элементов
        },
    });
});


// Явно устанавливаем CSRF-токен
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

// Проверяем, что токен установлен
console.log(axios.defaults.headers.common['X-CSRF-TOKEN']);


// Создаем ссылку на экземпляр Editor.js
const editor = ref(null)

// Создаем форму с помощью useForm
const form = useForm({
    title: null,
    body: null,
})





// Функция отправки формы
const submit = async () => {
    try {
        // Получаем данные из Editor.js
        const editorData = await editor.value.save()

        // Обновляем поле body данными из редактора
        form.body = JSON.stringify(editorData)

        console.log('Form data:', form) // Отладка: вывод данных формы

        // Отправляем данные на сервер
        form.post(route('posts.store'), {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Errors:', errors) // Отладка: вывод ошибок
            },
        })
    } catch (error) {
        console.error('Error saving editor data:', error)
    }
}
</script>

<template>
    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>Create a new post</h1>

        <form @submit.prevent="submit">
            <!-- Поле для заголовка -->
            <TextInput name="Title" v-model="form.title" :message="form.errors.title" />

            <!-- Контейнер для Editor.js -->
            <div id="editorjs" class="mt-4"></div>

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