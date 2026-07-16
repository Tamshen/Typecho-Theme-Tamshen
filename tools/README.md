# Theme build tools

Requires Node.js 18 or newer.

## Development

`static/js/theme.min.js` and `static/css/style.min.css` are generated release artifacts. Never edit them directly.

Before editing frontend assets, delete both minified files. The theme will then fall back to `static/js/theme.js` and `static/css/style.css` automatically. Edit only those source files and do not rebuild minified assets during normal development.

## Release

Generate minified files only when preparing a release:

```sh
cd tools
npm install
npm run build
npm run check
```

- `npm run build` generates `static/js/theme.min.js` and `static/css/style.min.css`.
- `npm run css:audit` reports potentially unused selectors without modifying source CSS.
- `npm run check` reports the JavaScript and CSS browser baseline, rejects critical unsupported CSS, and fails when generated files are stale.

The theme loads minified files when they exist and falls back to source files otherwise.

Supported baseline: Chrome/Edge 84, Firefox 78 ESR, and Safari 14.1. View Transitions and native image lazy loading are progressive enhancements.
