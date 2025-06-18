<template>
    <div class="stickers">
        <button v-for="sticker in stickers" :key="sticker.id" @click="toggleReaction(sticker.id)">

            <span :class="{ StickerActive: reactionCounts[sticker.id]?.count > 0, SingleSticker:1  }">{{ sticker.name
                }}</span> <br>{{ reactionCounts[sticker.id]?.count || 0 }}
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

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
    // Проверяем, авторизован ли пользователь
    if (!page.props.auth.user) {
        alert('Вы должны быть авторизованы, чтобы добавить реакцию.');
        return;
    }

    try {
        const response = await axios.post(`/posts/${props.PostId}/react`, {
            sticker_id: stickerId,
            'X-CSRF-TOKEN': page.props.csrf_token,
        });

        const { removed } = response.data;

        if (removed) {
            // Уменьшаем счётчик, если реакция была удалена
            if (reactionCounts.value[stickerId]) {
                reactionCounts.value[stickerId].count -= 1;

                // Если счётчик стал равен 0, удаляем запись
                if (reactionCounts.value[stickerId].count === 0) {
                    delete reactionCounts.value[stickerId];
                }
            }
            alert('Реакция удалена.');
        } else {
            // Увеличиваем счётчик, если реакция была добавлена
            if (!reactionCounts.value[stickerId]) {
                reactionCounts.value[stickerId] = { sticker_id: stickerId, count: 0 };
            }
            reactionCounts.value[stickerId].count += 1;
            alert('Реакция добавлена.');
        }
    } catch (error) {
        console.error(error);
        alert('Произошла ошибка.');
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