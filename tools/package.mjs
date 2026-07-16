import { createWriteStream } from 'node:fs';
import { mkdir, readFile, rm, stat } from 'node:fs/promises';
import { basename, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import archiver from 'archiver';

const toolsDir = dirname(fileURLToPath(import.meta.url));
const themeDir = resolve(toolsDir, '..');
const themeName = basename(themeDir);
const indexSource = await readFile(resolve(themeDir, 'index.php'), 'utf8');
const versionMatch = indexSource.match(/@version\s+([^\s*]+)/);

if (!versionMatch) throw new Error('Unable to find @version in index.php');

const version = versionMatch[1];
const distDir = resolve(themeDir, 'dist');
const outputFile = resolve(distDir, `${themeName}-${version}.zip`);
await mkdir(distDir, { recursive: true });
await rm(outputFile, { force: true });

const output = createWriteStream(outputFile);
const archive = archiver('zip', { zlib: { level: 9 } });
const completed = new Promise((resolvePromise, rejectPromise) => {
    output.on('close', resolvePromise);
    output.on('error', rejectPromise);
    archive.on('error', rejectPromise);
});

archive.pipe(output);
archive.glob('**/*', {
    cwd: themeDir,
    dot: true,
    ignore: [
        '.git/**',
        '.gitignore',
        '.DS_Store',
        '**/.DS_Store',
        'AGENTS.md',
        'dist/**',
        'tools/**'
    ]
}, { prefix: themeName });

await archive.finalize();
await completed;
const size = (await stat(outputFile)).size;
process.stdout.write(`${outputFile} (${(size / 1024 / 1024).toFixed(2)} MB)\n`);
