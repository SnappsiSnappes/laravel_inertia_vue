<script setup>
import { computed } from 'vue'

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
})

// Парсим JSON и преобразуем в удобный для отображения формат
const parsedBody = computed(() => {
    try {
        const parsed = JSON.parse(props.post.body)
        return parsed.blocks || []
    } catch (error) {
        console.error('Error parsing post body:', error)
        return []
    }
})

console.log(props.post)
</script>

<template>
    <div>
        <!-- Отображаем контент -->
        <div v-if="parsedBody.length">
            <div v-for="(block, index) in parsedBody" :key="index" class="mb-4">
                <!-- Заголовки -->
                <h2 v-if="block.type === 'header'" :class="`text-${block.data.level * 2}xl font-bold`">
                    {{ block.data.text }}
                </h2>

                <!-- Параграфы -->
                <p v-else-if="block.type === 'paragraph'" class="text-gray-700" v-html="block.data.text"></p>

                <!-- Неупорядоченные списки -->
                <ul v-else-if="block.type === 'list' && block.data.style === 'unordered'" class="list-disc pl-5">
                    <li v-for="(item, itemIndex) in block.data.items" :key="itemIndex">
                        {{ item.content }}
                    </li>
                </ul>

                <!-- Упорядоченные списки -->
                <ol v-else-if="block.type === 'list' && block.data.style === 'ordered'" class="list-decimal pl-5">
                    <li v-for="(item, itemIndex) in block.data.items" :key="itemIndex">
                        {{ item.content }}
                    </li>
                </ol>

                <!-- Чек-листы -->
                <ul v-else-if="block.type === 'list' && block.data.style === 'checklist'" class="pl-5">
                    <li v-for="(item, itemIndex) in block.data.items" :key="itemIndex" class="flex items-center">
                        <input type="checkbox" :checked="item.meta.checked" disabled class="mr-2">
                        <span :class="item.meta.checked ? 'line-through text-gray-500' : ''">
                            {{ item.content }}
                        </span>
                    </li>
                </ul>

                <!-- Цитаты -->
                <blockquote v-else-if="block.type === 'quote'" class="border-l-4 border-gray-300 pl-4 italic text-gray-600">
                    <p>{{ block.data.text }}</p>
                    <footer class="mt-2 text-sm text-gray-500">— {{ block.data.caption }}</footer>
                </blockquote>

                <!-- Изображения -->
                <figure v-else-if="block.type === 'image'" class="text-center">
                    <img :src="block.data.file.url" :alt="block.data.caption" class="mx-auto max-w-full h-auto">
                    <figcaption v-if="block.data.caption" class="mt-2 text-sm text-gray-500">{{ block.data.caption }}</figcaption>
                </figure>

                <!-- Код -->
                <pre v-else-if="block.type === 'code'" class="bg-gray-800 text-white p-4 rounded overflow-auto">
                    <code>{{ block.data.code }}</code>
                </pre>

                <!-- Таблицы -->
                <table v-else-if="block.type === 'table'" class="min-w-full bg-white border border-gray-300">
                    <tbody>
                        <tr v-for="(row, rowIndex) in block.data.content" :key="rowIndex">
                            <td v-for="(cell, cellIndex) in row" :key="cellIndex" class="border border-gray-300 p-2">
                                {{ cell }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Разделители -->
                <hr v-else-if="block.type === 'delimiter'" class="border-t border-gray-300 my-4">

                <!-- Встраиваемые элементы (YouTube, Vimeo и т.д.) -->
                <div v-else-if="block.type === 'embed'" class="relative w-full" :style="{ paddingTop: `${block.data.height}px` }">
                    <iframe
                        :src="block.data.embed"
                        frameborder="0"
                        allowfullscreen
                        class="absolute top-0 left-0 w-full h-full"
                    ></iframe>
                </div>

                <!-- Неизвестные блоки -->
                <div v-else class="text-red-500">
                    Unknown block type: {{ block.type }}
                </div>
            </div>
        </div>

        <p v-else class="text-red-500">Error loading content</p>
    </div>
</template>