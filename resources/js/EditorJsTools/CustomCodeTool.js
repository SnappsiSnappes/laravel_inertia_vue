// resources/js/EditroJsTools/CustomCodeTool.js
// (Note: fix typo in folder name → "EditorJsTools")

export default class CustomCodeTool {
    static get toolbox() {
        return {
            title: 'Code',
            icon: '<svg width="17" height="15" viewBox="0 0 33 25" xmlns="http://www.w3.org/2000/svg"><path d="m27.113 12.5-9.538 9.538a1.538 1.538 0 0 1-2.176 0L5.861 12.5a1.538 1.538 0 0 1 0-2.176L15.399 0.786a1.538 1.538 0 0 1 2.176 0l9.538 9.538a1.538 1.538 0 0 1 0 2.176z" fill="currentColor"/><path d="m27.113 17.5-9.538-9.538a1.538 1.538 0 0 0-2.176 0L5.861 17.5a1.538 1.538 0 0 0 0 2.176l9.538 9.538a1.538 1.538 0 0 0 2.176 0l9.538-9.538a1.538 1.538 0 0 0 0-2.176z" fill="currentColor"/></svg>'
        };
    }

    constructor({ data }) {
        // ✅ Load initial data
        this._data = {
            code: typeof data?.code === 'string' ? data.code : '',
            language: this._isValidLanguage(data?.language) ? data.language : 'plaintext'
        };
    }

    _isValidLanguage(lang) {
        const valid = ['plaintext', 'php', 'javascript', 'typescript', 'xml', 'css', 'json', 'python', 'bash', 'sql', 'yaml'];
        return valid.includes(lang);
    }

    render() {
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.flexDirection = 'column';
        wrapper.style.gap = '8px';

        // Language select
        const select = document.createElement('select');
        const languages = [
            { value: 'plaintext', label: 'Plain Text' },
            { value: 'php', label: 'PHP' },
            { value: 'javascript', label: 'JavaScript' },
            { value: 'typescript', label: 'TypeScript' },
            { value: 'xml', label: 'HTML' },
            { value: 'css', label: 'CSS' },
            { value: 'json', label: 'JSON' },
            { value: 'python', label: 'Python' },
            { value: 'bash', label: 'Bash' },
            { value: 'sql', label: 'SQL' },
            { value: 'yaml', label: 'YAML' },
        ];

        languages.forEach(lang => {
            const option = document.createElement('option');
            option.value = lang.value;
            option.textContent = lang.label;
            // ✅ Set selected based on initial data
            if (lang.value === this._data.language) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        // Textarea
        const textarea = document.createElement('textarea');
        textarea.placeholder = 'Enter code...';
        textarea.value = this._data.code; // ✅ Load initial code!
        textarea.style.width = '100%';
        textarea.style.minHeight = '100px';
        textarea.style.fontFamily = 'monospace';
        textarea.style.padding = '8px';
        textarea.style.border = '1px solid #ccc';
        textarea.style.borderRadius = '4px';

        // Update internal state on input
        textarea.addEventListener('input', () => {
            this._data.code = textarea.value;
        });

        select.addEventListener('change', () => {
            this._data.language = select.value;
        });

        wrapper.appendChild(select);
        wrapper.appendChild(textarea);
        return wrapper;
    }

    save(blockContent) {
        const select = blockContent.querySelector('select');
        const textarea = blockContent.querySelector('textarea');
        return {
            code: textarea.value.trim(),
            language: select.value,
        };
    }
}