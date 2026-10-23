import { describe, expect, it } from 'vitest';
import { escapeHtml } from '../../resources/js/lib/escape-html.js';

describe('escapeHtml', () => {
    it('maskiert alle Zeichen, die HTML oder Attribute öffnen', () => {
        expect(escapeHtml(`<a href="x" title='y'>&</a>`))
            .toBe('&lt;a href=&quot;x&quot; title=&#39;y&#39;&gt;&amp;&lt;/a&gt;');
    });

    it('lässt normalen Text und Zahlen unverändert', () => {
        expect(escapeHtml('Rahel Suter')).toBe('Rahel Suter');
        expect(escapeHtml(1234)).toBe('1234');
    });
});
