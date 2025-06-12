<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
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
import { usePage } from '@inertiajs/vue3';
// Доступ к данным страницы
const page = usePage();

const props = defineProps({
    initialData: {
        type: Object,
        
        // конвенция vue 3
        default: () => ({}), 
    },
});

const emit = defineEmits(['save']);

let editor = ref(null);

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
                        'X-CSRF-TOKEN': page.props.csrf_token,
                    },
                },
            },
            code: CodeTool,
            table: Table,
            delimiter: Delimiter,
            embed: Embed,
        },
        data: props.initialData,
    });
});



onUnmounted(() => {
    if (editor.value) {
        editor.value.destroy();
    }
});

// Метод для сохранения данных из редактора
const save = async () => {
    try {
        const editorData = await editor.value.save();
        emit('save', editorData);
    } catch (error) {
        console.error('Error saving editor data:', error);
    }
};

// используется родителем
defineExpose({ save }); 
</script>

<template>
    <div id="editorjs" class="editor-container mt-4"></div>
</template>