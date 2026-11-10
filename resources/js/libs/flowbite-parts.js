import { initAccordions } from 'flowbite/lib/esm/components/accordion';
import { initCollapses } from 'flowbite/lib/esm/components/collapse';
import { initDismisses } from 'flowbite/lib/esm/components/dismiss';
import { initDrawers } from 'flowbite/lib/esm/components/drawer';
import { initDropdowns } from 'flowbite/lib/esm/components/dropdown';

/**
 * Nur die Flowbite-Komponenten, die die Views verwenden: Hamburger-Menü (data-collapse-*), Kurs- und Benutzermenü
 * (data-dropdown-*), Hilfe rechts (data-drawer-*), Kapitel der Quali (data-accordion), Meldungen schliessen
 * (data-dismiss-target). `import 'flowbite'` lud alle 14 samt Datepicker (gut 200 KB).
 * Neue Flowbite-Attribute in Blade hier ergänzen. Browser: FlowbitePartsTest.
 */
export function initFlowbiteParts() {
    initCollapses();
    initDropdowns();
    initDrawers();
    initAccordions();
    initDismisses();
}
