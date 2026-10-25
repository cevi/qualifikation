// Erzeugt resources/css/libs/flowbite-base.css: die Grundstile aus flowbite/plugin (Formularfelder, Tooltips) ohne @layer.
// Aufruf: npm run css:flowbite-base (nach einem Flowbite-Update oder einer Änderung der Palette in app.css).
//
// Tailwind 4 legt die Grundstile von Plugins in @layer base; dort verlieren sie gegen Bootstrap (ohne Ebene).
// Unter Tailwind 3 standen sie nach Bootstrap und gewannen bei gleicher Spezifität. Die Kopie wird am Ende von
// legacy.css eingebunden und stellt diese Reihenfolge wieder her.
import { compile } from '@tailwindcss/node';
import { readFileSync, writeFileSync } from 'node:fs';

const target = 'resources/css/libs/flowbite-base.css';

// Palette aus app.css mitgeben: Farben in SVG-Hintergründen (Pfeil im Select) werden fest eingesetzt.
const palette = readFileSync('resources/css/app.css', 'utf8').match(/@theme \{[\s\S]*?\n\}/)[0];
const compiler = await compile(`@import 'tailwindcss/theme.css' layer(theme);\n${palette}\n@plugin 'flowbite/plugin';`, {
    base: process.cwd(),
    onDependency: () => {},
});
const css = compiler.build([]);

// Inhalt von @layer base { ... } herauslösen
const open = css.indexOf('{', css.indexOf('@layer base {'));
let depth = 0;
let close = -1;
for (let i = open; i < css.length; i++) {
    if (css[i] === '{') depth++;
    if (css[i] === '}' && --depth === 0) {
        close = i;
        break;
    }
}
let body = css.slice(open + 1, close).replace(/^ {2}/gm, '').trim();

// Flowbite 3.1.2 liefert z. B. "border-color: --color-gray-500;" (ungültig, der Browser ignoriert die Regel)
body = body.replace(/(:\s*)(--[a-z0-9-]+)(\s*(?:!important)?;)/g, '$1var($2)$3');

const version = JSON.parse(readFileSync('node_modules/flowbite/package.json', 'utf8')).version;
writeFileSync(target, `/*
 * Grundstile aus flowbite/plugin ${version} ohne @layer, erzeugt mit scripts/flowbite-base.mjs (npm run css:flowbite-base).
 * Nicht von Hand ändern. Unter Tailwind 3 standen sie nach Bootstrap und gewannen bei gleicher Spezifität
 * (z. B. [type=text] gegen .form-control); Tailwind 4 legt sie in @layer base. Entfernen, sobald Bootstrap weg ist.
 */
${body}
`);
console.log(`${target}: ${(body.match(/\{/g) || []).length} Regeln aus Flowbite ${version}`);
