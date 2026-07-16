import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { PurgeCSS } from 'purgecss';

const themeDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const result = await new PurgeCSS().purge({
    content: [
        resolve(themeDir, '*.php'),
        resolve(themeDir, 'function/**/*.php'),
        resolve(themeDir, 'static/js/theme.js')
    ],
    css: [resolve(themeDir, 'static/css/style.css')],
    rejected: true,
    rejectedCss: false,
    safelist: {
        standard: [
            'active', 'current', 'error', 'loading', 'next', 'open', 'prev', 'success',
            'typing', 'visible', 'word', 'protected', 'function_'
        ],
        deep: [
            /^comment-/, /^hljs-/, /^language-/, /^lang-/, /^mermaid/,
            /^page-navigator$/, /^task-list/, /^footnote/, /^toc-level-/
        ]
    }
});

const rejected = result.flatMap(item => item.rejected || []).sort();
process.stdout.write(`Potentially unused selectors: ${rejected.length}\n`);
for (const selector of rejected) process.stdout.write(`${selector}\n`);
