<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import ImageTool from '@editorjs/image';

// import CodeTool from '@editorjs/code';
import CustomCodeTool from '../../EditorJsTools/CustomCodeTool';

import Table from '@editorjs/table';
import Delimiter from '@editorjs/delimiter';
import Embed from '@editorjs/embed';
import axios from 'axios';
import paragraph from 'editorjs-paragraph-with-alignment';
import { usePage } from '@inertiajs/vue3';
import Underline from '@editorjs/underline';
import Warning from '@editorjs/warning';
import Marker from '@editorjs/marker';
import TextVariantTune from '@editorjs/text-variant-tune';
import Annotation from 'editorjs-annotation';
import AttachesTool from '@editorjs/attaches';
import Sortable from 'sortablejs';
import ImageGallery from '@kiberpro/editorjs-gallery';
import ColorPicker from 'editorjs-color-picker';


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
            gallery: {
                class: ImageGallery,
                config: {
                    sortableJs: Sortable,
                    endpoints: {
                        byFile: '/upload-image',
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': page.props.csrf_token,
                    },
                },
            },

            textVariant: TextVariantTune,

            Marker: {
                class: Marker,

            },


            warning: Warning,

            code: CustomCodeTool,


            underline: Underline,



            paragraph: {
                inlineToolbar: true,
                class: paragraph,
            },
            
            annotation: Annotation,

            header: Header,
            list: List,
            quote: Quote,
            image: {
                class: ImageTool,
                config: {
                    features: {
                        border: false,
                        caption: 'optional',
                        stretch: false,
                        withBackground: false
                    },
                    endpoints: {
                        byFile: '/upload-image',
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': page.props.csrf_token,
                    },
                },
            },
            table: Table,
            delimiter: Delimiter,
            embed: Embed,
            attaches: {
                class: AttachesTool,
                config: {
                    endpoint: '/upload-file',
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': page.props.csrf_token,
                    },

                }
            },

        },
        tunes: ['textVariant'],

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

console.log(props.initialData)
</script>

<template>
    <div id="editorjs" class="editor-container mt-4"></div>
</template>