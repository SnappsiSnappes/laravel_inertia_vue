<template>
    <div>
        <div v-for="(block, index) in parsedBody.blocks" :key="index" :class="['editor-block', alignmentClass(block)]">
            <!-- Параграф -->
            <div v-if="block.type === 'paragraph'" class="paragraph">
                {{ block.data.text }}
            </div>

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

            <!-- Цитаты -->
            <blockquote v-else-if="block.type === 'quote'" class="blockquote">
                <p>{{ block.data.text }}</p>
                <footer v-if="block.data.caption" class="text-sm text-slate-500 mt-2">
                    {{ block.data.caption }}
                </footer>
            </blockquote>

            <!-- Изображения -->
            <!-- Изображения -->
            <div v-else-if="block.type === 'image'" class="image" :class="{ 'stretched': block.data.stretched }">
                <img :src="block.data.file.url" :alt="block.data.caption" />
                <p v-if="block.data.caption" class="text-sm text-slate-500 mt-2 text-center">
                    {{ block.data.caption }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    post: Object,
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
</script>
