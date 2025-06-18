<template>
    <div v-if="message" class="flash-message" :class="type">
        <span>{{ message }}</span>
        <button class="close-button" @click="closeMessage">×</button>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    message: String,
    type: String,
});

const emit = defineEmits(['close']);

// Метод для закрытия сообщения
const closeMessage = () => {
    emit('close'); // Отправляем событие родительскому компоненту
};
</script>

<style scoped>
.flash-message {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 10px 20px;
    border-radius: 5px;
    color: white;
    font-size: 1rem;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 250px;
}

.close-button {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    margin-left: 10px;
}

.success {
    background-color: #28a745;
}

.error {
    background-color: #dc3545;
}

.info {
    background-color: #17a2b8;
}
</style>