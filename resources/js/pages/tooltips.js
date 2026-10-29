import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light-border.css';

/**
 * Erklärungen auf Quali und Vergleich (Kernkompetenzen, Bedeutung der Antworten): Antippen bzw. Klick auf ein
 * Element mit data-tooltip zeigt den Text. Immer als Text, nie als HTML (die Texte pflegen Admins und Kursleitende).
 * Ersetzt andcarpi/laravel-popper mit popper.js 1 und tippy.js 4 vom CDN.
 */
export function initTooltips(root = document) {
    const elements = [...root.querySelectorAll('[data-tooltip]')].filter((element) => element.dataset.tooltip.trim() !== '');

    return tippy(elements, {
        content: (element) => element.dataset.tooltip,
        allowHTML: false,
        trigger: 'click',
        placement: 'top-start',
        theme: 'light-border',
    });
}

initTooltips();
