<script setup>
import { computed, onMounted, nextTick, ref, onUnmounted } from "vue";
import ToggleReaction from "../Posts/ToggleReaction.vue";
import Gallery from "./ArticleBlocks/Gallery.vue";
import Image from "./ArticleBlocks/Image.vue";
import List from "./ArticleBlocks/List.vue";
import File from "./ArticleBlocks/File.vue";
import PreviewHero from "./PreviewHero.vue";
import Code from "./ArticleBlocks/Code.vue";
import DOMPurify from 'dompurify';

const props = defineProps({
    post: Object,
    IsAdmin: Boolean, // ← исправлено на camelCase
    authUser: Object,
});

// ✅ Настройка DOMPurify: разрешаем data-* атрибуты и классы
const purifyConfig = {
    ALLOW_DATA_ATTR: true, // разрешает data-title, data-text и др.
    ALLOWED_ATTR: ['class', 'data-title', 'data-text'], // явно разрешаем нужные атрибуты
};

const sanitizeHtml = (html) => {
    return DOMPurify.sanitize(html || "", purifyConfig);
};

const parsedBody = computed(() => {
    try {
        return JSON.parse(props.post.body);
    } catch (error) {
        console.error("Error parsing post body:", error);
        return { blocks: [] };
    }
});

const alignmentClass = (block) => {
    switch (block.data?.alignment) {
        case "center": return "text-center";
        case "right": return "text-right";
        default: return "text-left";
    }
};

// Popover state
const popover = ref({
    visible: false,
    title: "",
    description: "",
    x: 0,
    y: 0,
});

const showPopover = (e, title, description) => {
    const x = e.clientX + 10;
    const y = e.clientY - 90;

    // Защита от выхода за границы экрана
    const popoverWidth = 300;
    const popoverHeight = 100;
    const maxX = window.innerWidth - popoverWidth - 10;
    const maxY = window.innerHeight - popoverHeight - 10;

    const safeX = Math.max(5, Math.min(x, maxX));
    const safeY = Math.max(5, Math.min(y, maxY));

    popover.value = {
        visible: true,
        title: title || "Аннотация",
        description: description || "",
        x: safeX,
        y: safeY,
    };
};

const hidePopover = () => {
    popover.value.visible = false;
};

// 🧠 Умная обработка аннотаций
let annotationHandlers = [];

const attachAnnotationListeners = () => {
    // Находим все аннотации ВНУТРИ контейнера поста (а не во всём document!)
    const container = document.getElementById('post-content');
    if (!container) return;

    const annotations = container.querySelectorAll(".cdx-annotation");
    annotationHandlers = []; // сброс при повторном вызове

    annotations.forEach(el => {
        const title = el.getAttribute("data-title") || "";
        const desc = el.getAttribute("data-text") || "";

        const enter = (e) => showPopover(e, title, desc);
        const leave = () => hidePopover();

        el.addEventListener("mouseenter", enter);
        el.addEventListener("mouseleave", leave);

        annotationHandlers.push({ el, enter, leave });
    });
};

const cleanupAnnotationListeners = () => {
    annotationHandlers.forEach(({ el, enter, leave }) => {
        el.removeEventListener("mouseenter", enter);
        el.removeEventListener("mouseleave", leave);
    });
    annotationHandlers = [];
};

onMounted(async () => {
    await nextTick();
    const annotations = document.querySelectorAll(".cdx-annotation");
    annotations.forEach((el, index) => {
        const title = el.getAttribute("data-title") || "";
        const desc = el.getAttribute("data-text") || "";
        el.addEventListener("mouseenter", (e) => {
            showPopover(e, title, desc);
        });
        el.addEventListener("mouseleave", () => {
            hidePopover();
        });
    });
});

onUnmounted(() => {
    cleanupAnnotationListeners();
});
</script>

<template>

    <p>
        {{ props.post.body }}
    </p>



    <div class="w-full">

        <div class="py-5">
            <PreviewHero :post="props.post" :IsAdmin="props.IsAdmin" :authUser="props.authUser" />
            <p class="text-sm text-gray-500 pt-1">
                {{ post.humanReadableDate }} by user
                <span class="underline">{{ post.user.email }}</span>
            </p>
        </div>

        <div v-for="(block, index) in parsedBody.blocks" :key="index" :class="['editor-block', alignmentClass(block)]">
            <!-- Параграф -->
      <div v-if="block.type === 'paragraph'" class="paragraph" v-html="sanitizeHtml(block.data.text)"></div>

            <!-- Заголовки -->
            <div v-else-if="block.type === 'header'" :class="`header-${block.data.level}`">
                {{ block.data.text }}
            </div>

            <!-- Блок кода -->
            <div v-else-if="block.type === 'code'" class="">
                <Code :blockData="block.data" />
            </div>

            <!-- Списки -->
            <div v-else-if="block.type === 'list'" class="list">
                <List :BlockData="block.data" />
            </div>

            <!-- Изображения -->
            <div v-else-if="block.type === 'image'" class="image">
                <Image :ImageUrl="block.data.file.url" :caption="block.data.caption" :Width="block.data.file.width"
                    :Height="block.data.file.height" />
            </div>

            <!-- Галерея -->
            <div v-else-if="block.type === 'gallery'">
                <Gallery :files="block.data.files" :caption="block.data.caption" />
            </div>

            <!-- Отображение файлов -->
            <div v-else-if="block.type === 'attaches'" class="file-container">
                <File :file="block.data.file" />
            </div>

            <!-- Warning блок -->
            <div v-else-if="block.type === 'warning'" class="warning-div">
                <!-- Иконка предупреждения -->
                <svg xmlns="http://www.w3.org/2000/svg" class="warning-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!-- Заголовок (если есть) -->
                <strong v-if="block.data.title" class="warning-title">{{
                    block.data.title
                    }}</strong>

                <!-- Сообщение -->
                <p class="warning-p">{{ block.data.message }}</p>
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
                    class="w-1/2 h-auto aspect-video rounded-lg shadow-md mx-auto"></iframe>
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

    <ToggleReaction :PostId="props.post.id" />


  <!-- Popover -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      leave-active-class="transition-opacity duration-150"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div
        v-if="popover.visible"
        class="fixed z-[9999] bg-white p-3 rounded-lg shadow-lg border border-gray-200 max-w-xs text-sm text-gray-800"
        :style="{ left: popover.x + 'px', top: popover.y + 'px' }"
      >
        <h4 class="font-semibold text-gray-900">{{ popover.title }}</h4>
        <p class="mt-1 text-gray-600">{{ popover.description }}</p>
      </div>
    </Transition>
  </Teleport>
</template>

