import { initCollapses } from 'flowbite/lib/esm/components/collapse';
import { initDismisses } from 'flowbite/lib/esm/components/dismiss';
import { initDrawers } from 'flowbite/lib/esm/components/drawer';
import { initDropdowns } from 'flowbite/lib/esm/components/dropdown';

/**
 * Nur die Flowbite-Komponenten, die die Views verwenden: Hamburger-Menü (data-collapse-*), Kurs- und Benutzermenü
 * (data-dropdown-*), Hilfe rechts (data-drawer-*), Meldungen schliessen (data-dismiss-target). Die Kapitel der Quali
 * sind <details> und brauchen kein Akkordeon mehr. `import 'flowbite'` lud alle 14 samt Datepicker (gut 200 KB).
 * Neue Flowbite-Attribute in Blade hier ergänzen. Browser: FlowbitePartsTest.
 */
export function initFlowbiteParts() {
    initCollapses();
    initDropdowns();
    initDrawers();
    initDismisses();
}
