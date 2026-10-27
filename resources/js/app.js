/**
 * Auf jeder Seite geladen. Schwere Bibliotheken (Chart.js, Cropper) und Seitenskripte haben eigene
 * Einstiegspunkte in vite.config.js und werden per @vite nur in ihren Ansichten eingebunden.
 */

import './bootstrap';
import 'bootstrap';
import 'laravel-datatables-vite';
import 'flowbite';
import './theme-toggle.js';
