// Erzeugt resources/css/libs/fontawesome-icons.css: Font Awesome ohne die rund 1900 Icons, die das Tool nicht braucht.
// Aufruf: npm run css:fontawesome (nach einem neuen Icon in Views/JS/PHP oder einem Font-Awesome-Update).
//
// fontawesome.css besteht aus Grundregeln (.fa, .fas, Grössen, Animationen) und je einer Regel pro Icon
// (".fa-house { --fa: "\f015"; }"). Behalten werden alle Grundregeln und die Icons, deren Klasse im Code vorkommt.
// Die Schriften (solid.css, regular.css) bleiben unverändert. tests/js/fontawesome-icons.test.js prüft, dass die
// Datei zum Code passt.
import { readFileSync, readdirSync, statSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { pathToFileURL } from 'node:url';

export const SOURCE = 'node_modules/@fortawesome/fontawesome-free/css/fontawesome.css';
export const TARGET = 'resources/css/libs/fontawesome-icons.css';
const SCAN = ['resources/views', 'resources/js', 'app'];
const EXTENSIONS = /\.(php|js|vue)$/;

/**
 * Oberste Regeln der Datei: { selector, body, text }. Verschachtelte Blöcke (@keyframes, @media) bleiben ganz;
 * Kommentare zählen zur folgenden Regel.
 */
export function rules(css) {
    const result = [];
    let start = 0;
    let depth = 0;
    let open = -1;
    for (let i = 0; i < css.length; i++) {
        if (css.startsWith('/*', i)) {
            i = css.indexOf('*/', i + 2) + 1;
        } else if (css[i] === '{') {
            if (depth++ === 0) {
                open = i;
            }
        } else if (css[i] === '}' && --depth === 0) {
            const text = css.slice(start, i + 1).trim();
            const selector = css.slice(start, open).replace(/\/\*[\s\S]*?\*\//g, '').trim();
            result.push({ selector, body: css.slice(open + 1, i).trim(), text });
            start = i + 1;
        }
    }
    return result;
}

/** Eine Icon-Regel hat genau eine Klasse und setzt --fa (das Zeichen). */
export function iconName(rule) {
    const match = rule.selector.match(/^\.(fa-[a-z0-9-]+)$/);
    return match && /--fa:\s*"/.test(rule.body) ? match[1] : null;
}

/** Alle fa-…-Klassen in den Quelltexten. */
export function usedClasses(contents) {
    const classes = new Set();
    for (const content of contents) {
        for (const [name] of content.matchAll(/\bfa-[a-z0-9]+(?:-[a-z0-9]+)*/g)) {
            classes.add(name);
        }
    }
    return [...classes].sort();
}

/** Grundregeln plus die gebrauchten Icons, in der Reihenfolge der Vorlage. */
export function subset(css, classes) {
    const wanted = new Set(classes);
    return rules(css)
        .filter((rule) => {
            const name = iconName(rule);
            return name === null || wanted.has(name);
        })
        .map((rule) => rule.text)
        .join('\n\n');
}

export function sourceContents(root = '.') {
    const contents = [];
    const walk = (dir) => {
        for (const entry of readdirSync(dir)) {
            const path = join(dir, entry);
            if (statSync(path).isDirectory()) {
                walk(path);
            } else if (EXTENSIONS.test(entry)) {
                contents.push(readFileSync(path, 'utf8'));
            }
        }
    };
    SCAN.forEach((dir) => walk(join(root, dir)));
    return contents;
}

export function build(root = '.') {
    const css = readFileSync(join(root, SOURCE), 'utf8');
    const version = JSON.parse(readFileSync(join(root, 'node_modules/@fortawesome/fontawesome-free/package.json'), 'utf8')).version;
    return `/*
 * Font Awesome Free ${version} (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License), nur die im Tool verwendeten Icons.
 * Erzeugt mit scripts/fontawesome-icons.mjs (npm run css:fontawesome). Nicht von Hand ändern.
 */
${subset(css, usedClasses(sourceContents(root)))}
`;
}

if (import.meta.url === pathToFileURL(process.argv[1] ?? '').href) {
    writeFileSync(TARGET, build());
    console.log(`${TARGET} geschrieben`);
}
