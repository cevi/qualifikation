import { readFileSync } from 'node:fs';
import { describe, expect, it } from 'vitest';
import { build, iconName, rules, SOURCE, sourceContents, subset, TARGET, usedClasses } from '../../scripts/fontawesome-icons.mjs';

const full = readFileSync(SOURCE, 'utf8');
const allSelectors = new Set(rules(full).flatMap((rule) => rule.selector.split(',').map((part) => part.trim())));

describe('Font Awesome nur mit den gebrauchten Icons', () => {
    it('die erzeugte Datei passt zum Code (sonst: npm run css:fontawesome)', () => {
        expect(readFileSync(TARGET, 'utf8')).toBe(build());
    });

    it('jede fa-Klasse im Code gibt es in Font Awesome (Tippfehler zeigen sonst ein leeres Kästchen)', () => {
        const unknown = usedClasses(sourceContents()).filter((name) => !allSelectors.has(`.${name}`));

        expect(unknown).toEqual([]);
    });

    it('behält Grundregeln und Animationen, wirft ungebrauchte Icons weg', () => {
        const css = `.fa { display: inline-block; }
@keyframes fa-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.fa-house {
  --fa: "\\f015"; }
.fa-anchor {
  --fa: "\\f13d"; }`;

        const result = subset(css, ['fa-house', 'fa-solid']);

        expect(result).toContain('.fa { display: inline-block; }');
        expect(result).toContain('100% { transform: rotate(360deg); } }');
        expect(result).toContain('.fa-house');
        expect(result).not.toContain('.fa-anchor');
        expect(rules(css).map(iconName)).toEqual([null, null, 'fa-house', 'fa-anchor']);
    });
});
