<!-- Code.vue -->
<script setup>
import { ref, onMounted, watch } from 'vue';
import hljs from 'highlight.js/lib/core';

// Languages
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

const props = defineProps({
    blockData: { type: Object, required: true }
});

const highlightedCode = ref('');
const languageLabel = ref('');
const currentTheme = ref('github-dark'); // default

// Theme options
const themes = [
    { id: 'github-dark', name: 'GitHub Dark' },
    { id: 'github', name: 'GitHub Light' },
    { id: 'atom-one-dark', name: 'Atom One Dark' },
    { id: 'atom-one-light', name: 'Atom One Light' },
    { id: 'stackoverflow-dark', name: 'Stack Overflow Dark' },
];

// Load saved theme from localStorage
onMounted(() => {
    const saved = localStorage.getItem('code_theme');
    if (saved && themes.some(t => t.id === saved)) {
        currentTheme.value = saved;
    }
    loadThemeCSS(currentTheme.value);
    renderCode();
});

// Watch for theme changes
watch(currentTheme, (newTheme) => {
    localStorage.setItem('code_theme', newTheme);
    loadThemeCSS(newTheme);
    renderCode();
});

async function loadThemeCSS(themeId) {
    // Clear previous theme (optional: not strictly needed due to scoped nature)
    const themeMap = {
        'github-dark': () => import('highlight.js/styles/github-dark.css'),
        'github': () => import('highlight.js/styles/github.css'),
        'atom-one-dark': () => import('highlight.js/styles/atom-one-dark.css'),
        'atom-one-light': () => import('highlight.js/styles/atom-one-light.css'),
        'stackoverflow-dark': () => import('highlight.js/styles/stackoverflow-dark.css'),
    };

    if (themeMap[themeId]) {
        await themeMap[themeId]();
    }
}

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
</script>

<template>
  <div class="code-container my-6">
    <!-- Theme selector (only shown in non-production or for admins if you want) -->
    <!-- For now, always show. You can wrap in v-if="isAdmin" later -->
    <div class="theme-selector flex justify-between items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-lg">
      <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
        {{ languageLabel !== 'Text' ? languageLabel : 'Code' }}
      </span>
      <select
        v-model="currentTheme"
        class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500"
      >
        <option v-for="theme in themes" :key="theme.id" :value="theme.id">
          {{ theme.name }}
        </option>
      </select>
    </div>

    <pre class="code-block !my-0"><code v-html="highlightedCode"></code></pre>
  </div>
</template>

<style scoped>
.code-container {
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
}

.code-block {
  margin: 0;
  padding: 1.25rem;
  font-family: 'Fira Code', 'JetBrains Mono', Consolas, monospace;
  font-size: 0.875rem;
  line-height: 1.6;
  overflow-x: auto;
  border-radius: 0 0 0.75rem 0.75rem;
}

.code-block code {
  display: block;
  white-space: pre;
  tab-size: 4;
}
</style>