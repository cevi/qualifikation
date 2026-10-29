/**
 * Auf jeder Seite geladen. Schwere Bibliotheken (Chart.js, Cropper, Vue) und Seitenskripte haben eigene
 * Einstiegspunkte in vite.config.js und werden per @vite nur in ihren Ansichten eingebunden.
 */

import 'flowbite';
import './theme-toggle.js';
import './confirm-submit.js';
import './bottom-nav.js';
import './pwa.js';
