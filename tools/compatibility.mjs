import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const themeDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const js = await readFile(resolve(themeDir, 'static/js/theme.js'), 'utf8');
const css = await readFile(resolve(themeDir, 'static/css/style.css'), 'utf8');
const checks = [
    ['Fetch API', /\bfetch\s*\(/, 'Chrome 42, Firefox 39, Safari 10.1'],
    ['Pointer Events', /pointer(?:down|move|up|cancel)/, 'Chrome 55, Firefox 59, Safari 13'],
    ['Element.closest', /\.closest\s*\(/, 'Chrome 41, Firefox 35, Safari 9'],
    ['View Transitions', /@view-transition/, 'Progressive enhancement; unsupported browsers use normal navigation'],
    ['Native lazy images', /\.loading\s*=\s*['"]lazy/, 'Progressive enhancement; unsupported browsers load images normally']
];

const cssFailures = [];
if (/:has\s*\(/.test(css)) cssFailures.push('CSS :has() exceeds the supported browser baseline');
if (/\d+dvh/.test(css) && !/\d+vh[^}]+\d+dvh/s.test(css)) {
    cssFailures.push('CSS dynamic viewport units require a vh fallback declared first');
}
if (/backdrop-filter/.test(css) && !/-webkit-backdrop-filter/.test(css)) {
    cssFailures.push('backdrop-filter requires a -webkit-backdrop-filter fallback');
}

process.stdout.write('Browser baseline: Chrome/Edge 84, Firefox 78 ESR, Safari 14.1\n');
for (const [name, pattern, note] of checks) {
    if (pattern.test(js) || pattern.test(css)) process.stdout.write(`- ${name}: ${note}\n`);
}
process.stdout.write('- CSS Flex Gap: baseline requires Chrome/Edge 84 and Safari 14.1\n');
process.stdout.write('- CSS logical properties and inset: compiled by Lightning CSS for configured targets\n');

if (cssFailures.length) {
    for (const failure of cssFailures) process.stderr.write(`CSS compatibility error: ${failure}\n`);
    process.exitCode = 1;
}
