<template>
    <div v-if="message.message" class="flash-message" :class="message.type">
        <span>{{ message.message }}</span>
        <span v-if="remainingTime > 0" class="ps-5">{{ remainingTime }}</span>
        <button class="close-button" @click="closeMessage">×</button>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, onMounted, onUnmounted, ref, watch } from 'vue';



const props = defineProps({
    message: Object,
});

console.log(props);


const emit = defineEmits(['close']);

// Таймер для автоматического закрытия
let timer = null;
let interval = null;

// Оставшееся время в секундах
const remainingTime = ref(5);

// Функция для запуска таймера
const startTimer = () => {
    // Сбрасываем предыдущие таймеры
    if (timer) clearTimeout(timer);
    if (interval) clearInterval(interval);

    // Устанавливаем начальное значение времени
    remainingTime.value = 5;

    // Запускаем интервал для обновления времени
    interval = setInterval(() => {
        if (remainingTime.value > 0) {
            remainingTime.value--;
        }
    }, 1000);

    // Устанавливаем таймер на 5 секунд
    timer = setTimeout(() => {
        closeMessage();
    }, 5000);
};

// Метод для закрытия сообщения
const closeMessage = () => {
    if (timer) clearTimeout(timer); // Очищаем таймер
    if (interval) clearInterval(interval); // Очищаем интервал
    emit('close'); // Отправляем событие родительскому компоненту
};

// При монтировании компонента запускаем таймер
onMounted(() => {
    if (props.message) {
        startTimer();
    }
});

// При размонтировании компонента очищаем ресурсы
onUnmounted(() => {
    if (timer) clearTimeout(timer);
    if (interval) clearInterval(interval);
});

// Следим за изменением props.message
watch(
    () => props.message,
    (newMessage) => {
        if (newMessage) {
            startTimer(); // Перезапускаем таймер при появлении нового сообщения
        }
    }
);
</script>
