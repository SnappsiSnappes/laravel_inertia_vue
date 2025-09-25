<!-- Code.vue -->
<script setup>
// ✅ Import theme CSS right here — no need in app.js!

import { ref, onMounted } from 'vue';
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
import 'highlight.js/styles/github-dark.css';

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

onMounted(() => {
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
});
</script>

<template>
  <div class="code-container my-6">
    <div 
      v-if="languageLabel !== 'Text'" 
      class="code-header px-4 py-2 rounded-t-lg"
    >
      <span class="text-xs font-medium">{{ languageLabel }}</span>
    </div>
    <pre class="code-block !my-0"><code v-html="highlightedCode"></code></pre>
  </div>
</template>

<style scoped>
/* Container */
.code-container {
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
  background: #0d1117; /* Matches github-dark background */
}

/* Header */
.code-header {
  background: #161b22; /* github-dark header color */
  color: #58a6ff; /* github-blue */
}

/* Pre + Code */
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