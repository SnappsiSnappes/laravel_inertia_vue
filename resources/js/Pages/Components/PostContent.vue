<script setup>
import { computed } from 'vue';

const props = defineProps({
    post: Object,
    IsAdmin: Boolean,
    authUser: Object,
});




// Разбор JSON-данных
const parsedBody = computed(() => {
    try {
        return JSON.parse(props.post.body);
    } catch (error) {
        console.error('Error parsing post body:', error);
        return { blocks: [] };
    }
});

// Определение классов выравнивания
const alignmentClass = (block) => {
    switch (block.data.alignment) {
        case 'center':
            return 'text-center';
        case 'right':
            return 'text-right';
        default:
            return 'text-left';
    }
};

console.log(props.post.body)
</script>

<template>
    <p class="text-sm text-gray-500">{{ post.humanReadableDate }} by user <span class="underline">{{ post.user.email
            }}</span></p>

    <div class="">
        <h1 class="py-10">{{ post.title }}</h1>

        <div class="PreviewDiv" id="preview">

            <p>{{ post.preview_text }}</p>
            <div v-if="props.post.preview_image" class="image">
                <img width="200px" :src="props.post.preview_image" />
            </div>

        </div>


        <div v-for="(block, index) in parsedBody.blocks" :key="index" :class="['editor-block', alignmentClass(block)]">

            <!-- Параграф -->
            <div v-if="block.type === 'paragraph'" class="paragraph" v-html="block.data.text"></div>

            <!-- Заголовки -->
            <div v-else-if="block.type === 'header'" :class="`header-${block.data.level}`">
                {{ block.data.text }}
            </div>

            <!-- Списки -->
            <div v-else-if="block.type === 'list'" class="list">
                <!-- Чек-лист -->
                <ul v-if="block.data.style === 'checklist'">
                    <li v-for="(item, idx) in block.data.items" :key="idx" class="flex items-center">
                        <input type="checkbox" :checked="item.meta?.checked" disabled class="mr-2" />
                        <span>{{ item.content }}</span>
                    </li>
                </ul>

                <!-- Маркированный список -->
                <ul v-else-if="block.data.style === 'unordered'">
                    <li v-for="(item, idx) in block.data.items" :key="idx">{{ item.content }}</li>
                </ul>

                <!-- Нумерованный список -->
                <ol v-else>
                    <li v-for="(item, idx) in block.data.items" :key="idx">{{ item.content }}</li>
                </ol>
            </div>

            <!-- Изображения -->
            <div v-else-if="block.type === 'image'" class="image" :class="{ 'stretched': block.data.stretched }">
                <img :src="block.data.file.url" :alt="block.data.caption" />
                <p v-if="block.data.caption" class="text-sm text-slate-500 mt-2 text-center">
                    {{ block.data.caption }}
                </p>
            </div>

            <!-- Warning блок -->
            <div v-else-if="block.type === 'warning'"
                class="warning-block relative bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
                <!-- Иконка предупреждения -->
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-4 left-4 w-8 h-8 text-yellow-500 opacity-75"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!-- Заголовок (если есть) -->
                <strong v-if="block.data.title" class="block font-bold mb-2 pl-16">{{ block.data.title }}</strong>

                <!-- Сообщение -->
                <p class="text-gray-800 pl-16">{{ block.data.message }}</p>
            </div>


            <!-- Цитаты -->
            <div v-else-if="block.type === 'quote'"
                class="quote-block relative bg-gray-50 border-l-4 border-gray-300 p-6 rounded-lg">
                <!-- Иконка кавычек -->
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-4 left-4 w-8 h-8 text-gray-400 opacity-75"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>

                <!-- Текст цитаты -->
                <p class="text-gray-800 pl-16">{{ block.data.text }}</p>

                <!-- Подпись (если есть) -->
                <footer v-if="block.data.caption" class="text-sm text-gray-500 mt-2 pl-16">
                    — {{ block.data.caption }}
                </footer>
            </div>

            <!-- Встраиваемый контент (embed) -->
            <div v-else-if="block.type === 'embed'" class="embed-block relative">
                <iframe :src="block.data.embed" frameborder="0" allowfullscreen
                    class="w-1/2   h-auto aspect-video rounded-lg shadow-md mx-auto"></iframe>
                <p v-if="block.data.caption" class="text-sm text-gray-700 mt-2 text-center">
                    {{ block.data.caption }}
                </p>
            </div>

            <!-- Таблица -->
            <div v-else-if="block.type === 'table'" class="table-block my-4">
                <table class="w-full border-collapse border border-gray-300">
                    <tbody>
                        <tr v-for="(row, rowIndex) in block.data.content" :key="rowIndex"
                            class="border border-gray-300">
                            <td v-for="(cell, cellIndex) in row" :key="cellIndex"
                                class="p-2 border border-gray-300 text-center">
                                {{ cell }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</template>
