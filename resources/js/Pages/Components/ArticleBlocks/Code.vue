<!-- Code.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import hljs from 'highlight.js/lib/core';

// Языки
import php from 'highlight.js/lib/languages/php';
import javascript from 'highlight.js/lib/languages/javascript';
import typescript from 'highlight.js/lib/languages/typescript';
import css from 'highlight.js/lib/languages/css';
import xml from 'highlight.js/lib/languages/xml';
import json from 'highlight.js/lib/languages/json';
import python from 'highlight.js/lib/languages/python';
import bash from 'highlight.js/lib/languages/bash';
import sql from 'highlight.js/lib/languages/sql';
import yaml from 'highlight.js/lib/languages/yaml';

// Регистрация языков
hljs.registerLanguage('php', php);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('typescript', typescript);
hljs.registerLanguage('css', css);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('json', json);
hljs.registerLanguage('python', python);
hljs.registerLanguage('bash', bash);
hljs.registerLanguage('sql', sql);
hljs.registerLanguage('yaml', yaml);

// Heroicons
import { ClipboardIcon } from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/24/solid';

// Статический импорт темы
import 'highlight.js/styles/monokai.css';

const props = defineProps({
  blockData: { type: Object, required: true }
});

const highlightedCode = ref('');
const languageLabel = ref('');
const copied = ref(false);

onMounted(() => {
  renderCode();
});

function renderCode() {
  const { code, language } = props.blockData;

  const langLabels = {
    plaintext: 'Text',
    php: 'PHP',
    javascript: 'JavaScript',
    typescript: 'TypeScript',
    xml: 'HTML',
    css: 'CSS',
    json: 'JSON',
    python: 'Python',
    bash: 'Bash',
    sql: 'SQL',
    yaml: 'YAML'
  };

  languageLabel.value = langLabels[language] || language;

  let result;
  if (language && language !== 'plaintext' && hljs.listLanguages().includes(language)) {
    result = hljs.highlight(code, { language });
  } else {
    result = hljs.highlightAuto(code);
  }

  highlightedCode.value = result.value;
}

async function copyToClipboard() {
  try {
    await navigator.clipboard.writeText(props.blockData.code);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 1500);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
}
</script>

<template>
  <div class="code-container my-6">
    <div class="code-header flex justify-between items-center px-4 py-2 bg-[#34352f] rounded-t-lg">
      <span class="text-xs font-medium text-gray-300 font-jetbrains">
        {{ languageLabel !== 'Text' ? languageLabel : 'Code' }}
      </span>

      <!-- Кнопка копирования с Heroicons -->
      <button
        @click="copyToClipboard"
        class="text-gray-400 hover:text-gray-200 transition-colors focus:outline-none"
        :disabled="copied"
        aria-label="Copy code to clipboard"
      >
        <component
          :is="copied ? CheckIcon : ClipboardIcon"
          class="h-4 w-4"
          :class="{ 'text-green-400': copied }"
        />
      </button>
    </div>
    <pre class="hljs !my-0 rounded-b-lg"><code v-html="highlightedCode"></code></pre>
  </div>
</template>

<style scoped>
.code-container {
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
}

.hljs {
  margin: 0;
  padding: 1.25rem !important;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.875rem;
  line-height: 1.6;
  overflow-x: auto;
}
</style>