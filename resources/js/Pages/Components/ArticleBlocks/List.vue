<script setup>
const props = defineProps({
    BlockData:Object
})

</script>

<template>
    <div class="list">
        <!-- Чек-лист -->
        <ul v-if="BlockData.style === 'checklist'">
            <li v-for="(item, idx) in BlockData.items" :key="idx" class="flex items-center">
                <input type="checkbox" :checked="item.meta?.checked" disabled class="mr-2" />
                <span>{{ item.content }}</span>
            </li>
        </ul>

        <!-- Маркированный список -->
        <ul v-else-if="BlockData.style === 'unordered'">
            <li v-for="(item, idx) in BlockData.items" :key="idx">{{ item.content }}</li>
        </ul>

        <!-- Нумерованный список -->
        <ol v-else>
            <li v-for="(item, idx) in BlockData.items" :key="idx">{{ item.content }}</li>
        </ol>
    </div>
</template>