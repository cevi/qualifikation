/**
 * Hell/Dunkel-Umschalter im Menü (components/layouts/toggle-switch). Klasse "dark" auf <html> und das passende Symbol
 * setzen schon die Inline-Skripte in includes/header und in der Komponente, damit nichts aufblitzt; hier nur der
 * Klick. Die Wahl bleibt in localStorage ("color-theme"), ohne Wahl gilt die Einstellung des Systems.
 */
const button = document.getElementById('theme-toggle');

button?.addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('color-theme', dark ? 'dark' : 'light');
    // Im dunklen Modus zeigt der Knopf die Sonne (zu hell wechseln), sonst den Mond.
    document.getElementById('theme-toggle-light-icon').classList.toggle('hidden', !dark);
    document.getElementById('theme-toggle-dark-icon').classList.toggle('hidden', dark);
});
