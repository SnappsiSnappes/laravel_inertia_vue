<template>
    <div class="stickers">
        <button v-for="sticker in stickers" :key="sticker.id" @click="toggleReaction(sticker.id)"
            :data-sticker-id="sticker.id">
            <span class="SingleSticker" :class="{ StickerActive: reactionCounts[sticker.id]?.count > 0 }">
                {{ sticker.name }}
            </span> <br>{{ reactionCounts[sticker.id]?.count || 0 }}
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import FlashMessage from '../Components/FlashMessage.vue';

const page = usePage();

const props = defineProps({
    PostId: Number,
});

// Список стикеров
const stickers = ref([
    { id: 'smile', name: '😊' },
    { id: 'heart', name: '❤️' },
    { id: 'laugh', name: '😂' },
    { id: 'text', name: '***НЯ' },
]);

// Объект для хранения количества реакций
const reactionCounts = ref({});

// Метод для переключения реакции

const toggleReaction = async (stickerId) => {
    if (!page.props.auth.user) {
        page.props.flash = { message: 'Вы должны быть авторизованы, чтобы добавить реакцию.', type: 'error' };
        return;
    }

    try {
        const response = await axios.post(`/posts/${props.PostId}/react`, {
            sticker_id: stickerId,
            'X-CSRF-TOKEN': page.props.csrf_token,
        });

        console.log(response.data);
        const { message, removed } = response.data;

        // Обновляем флеш-сообщение
        page.props.flash.message = message;

        // Обновляем счётчик реакций
        if (removed) {
            if (reactionCounts.value[stickerId]) {
                reactionCounts.value[stickerId].count -= 1;

                if (reactionCounts.value[stickerId].count === 0) {
                    delete reactionCounts.value[stickerId];
                }
            }
        } else {
            if (!reactionCounts.value[stickerId]) {
                reactionCounts.value[stickerId] = { sticker_id: stickerId, count: 0 };
            }
            reactionCounts.value[stickerId].count += 1;
        }
    } catch (error) {
        console.error(error);
        page.props.flash.message = { message: 'Произошла ошибка.', type: 'error' };
    }
};

// Метод для загрузки данных о реакциях
const loadReactions = async () => {
    try {
        const response = await axios.get(`/posts/${props.PostId}/reactions`);
        reactionCounts.value = response.data; // Обновляем объект с количеством реакций
    } catch (error) {
        console.error('Ошибка при загрузке реакций:', error);
    }
};

// Загружаем данные о реакциях при монтировании компонента
onMounted(() => {
    loadReactions();
});
</script>