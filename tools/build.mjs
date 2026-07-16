import { readFile, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import browserslist from 'browserslist';
import { transform as transformCss, browserslistToTargets } from 'lightningcss';
import { transform as transformJs } from 'esbuild';

const toolsDir = dirname(fileURLToPath(import.meta.url));
const themeDir = resolve(toolsDir, '..');
const targets = ['chrome >= 84', 'edge >= 84', 'firefox >= 78', 'safari >= 14.1'];
const files = [
    {
        source: resolve(themeDir, 'static/js/theme.js'),
        output: resolve(themeDir, 'static/js/theme.min.js'),
        type: 'js'
    },
    {
        source: resolve(themeDir, 'static/css/style.css'),
        output: resolve(themeDir, 'static/css/style.min.css'),
        type: 'css'
    }
];

async function compile(file) {
    const source = await readFile(file.source, 'utf8');
    if (file.type === 'js') {
        const result = await transformJs(source, {
            target: ['chrome84', 'edge84', 'firefox78', 'safari14.1'],
            minify: true,
            legalComments: 'none',
            charset: 'utf8'
        });
        return result.code;
    }
    const result = transformCss({
        filename: file.source,
        code: Buffer.from(source),
        minify: true,
        targets: browserslistToTargets(browserslist(targets))
    });
    return result.code.toString();
}

async function build(checkOnly = false) {
    let stale = false;
    for (const file of files) {
        const output = await compile(file);
        if (checkOnly) {
            const current = await readFile(file.output, 'utf8').catch(() => '');
            if (current !== output) {
                stale = true;
                process.stderr.write(`${file.output} is out of date\n`);
            }
        } else {
            await writeFile(file.output, output);
            process.stdout.write(`${file.output} (${Buffer.byteLength(output)} bytes)\n`);
        }
    }
    if (stale) process.exitCode = 1;
}

const checkOnly = process.argv.includes('--check');
await build(checkOnly);
