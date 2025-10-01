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

// Статический импорт темы (лучше, чем динамический в onMounted)
import 'highlight.js/styles/monokai.css'; 
//gradient-light.css rofl
//monokai.css 10/10
//#! all fonts https://github.com/highlightjs/highlight.js/tree/main/src/styles
const props = defineProps({
  blockData: { type: Object, required: true }
});

const highlightedCode = ref('');
const languageLabel = ref('');

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
</script>

<template>
  <div class="code-container my-6">
    <div class="code-header px-4 py-2 bg-gray-800 rounded-t-lg">
      <span class="text-xs font-medium text-gray-300">
        {{ languageLabel !== 'Text' ? languageLabel : 'Code' }}
      </span>
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

/* Важно: не переопределяем padding, font-size и т.д. */
.hljs {
  margin: 0;
  padding: 1.25rem !important; /* если нужно — но осторожно! */
  font-family: 'JetBrains Mono';
  font-size: 0.875rem;
  line-height: 1.6;
  overflow-x: auto;
}
</style>