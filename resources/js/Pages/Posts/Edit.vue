<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import ImageTool from '@editorjs/image';
import CodeTool from '@editorjs/code';
import Table from '@editorjs/table';
import Delimiter from '@editorjs/delimiter';
import Embed from '@editorjs/embed';
import axios from 'axios';
import paragraph from 'editorjs-paragraph-with-alignment';

const props = defineProps({
    post: Object,
});



onMounted(() => {
    editor.value = new EditorJS({
        holder: 'editorjs',
        tools: {

            paragraph: {
      class: paragraph,
      inlineToolbar: true,

            },
            header: Header,
            list: List,
            quote: Quote,
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: '/upload-image',
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': axios.defaults.headers.common['X-CSRF-TOKEN'],
                    },
                },
            },
            code: CodeTool,
            table: Table,
            delimiter: Delimiter,
            embed: Embed,
        },
        data: JSON.parse(props.post.body),
    });
});

onUnmounted(() => {
    if (editor.value) {
        editor.value.destroy();
    }
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

const editor = ref(null);

const form = useForm({
    title: props.post.title,
    body: props.post.body,
});

const submit = async () => {
    try {
        const editorData = await editor.value.save();
        form.body = JSON.stringify(editorData);

        form.put(route('posts.update', props.post.id), {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Errors:', errors);
            },
        });
    } catch (error) {
        console.error('Error saving editor data:', error);
    }
};
</script>

<template>

    <Head :title="`${$page.component}`" />
    <div class="w-2/4 mx-auto">
        <h1>Create a new post</h1>

        <!-- Форма для заголовка -->
        <form @submit.prevent="submit">
            <TextInput name="Title" v-model="form.title" :message="form.errors.title" />

            <div v-if="form.errors.body" class="text-red-500 mt-2">{{ form.errors.body }}</div>

            <div class="mt-4">
                <button class="primary-btn" type="submit" :disabled="form.processing">
                    Update Post
                </button>
            </div>
        </form>
        <!-- Контейнер для Editor.js -->
        <div id="editorjs" class="editor-container mt-4"></div>

    </div>
</template>