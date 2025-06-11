<template>
    <div>
        <div v-for="(block, index) in parsedBody.blocks" :key="index" :class="['editor-block', alignmentClass(block)]">
            <div v-if="block.type === 'paragraph'" class="paragraph">
                {{ block.data.text }}
            </div>
            <div v-else-if="block.type === 'header'" :class="`header-${block.data.level}`">
                {{ block.data.text }}
            </div>
            <div v-else-if="block.type === 'list'" class="list">
                <ul v-if="block.data.style === 'unordered'">
                    <li v-for="(item, idx) in block.data.items" :key="idx">{{ item }}</li>
                </ul>
                <ol v-else>
                    <li v-for="(item, idx) in block.data.items" :key="idx">{{ item }}</li>
                </ol>
            </div>
            <div v-else-if="block.type === 'image'" class="image">
                <img :src="block.data.file.url" :alt="block.data.caption" />
                <p v-if="block.data.caption">{{ block.data.caption }}</p>
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

