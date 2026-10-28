/**
 * "Mehr" im Menü unten (includes/bottomnav.blade.php) öffnet das bestehende Menü oben, statt ein zweites zu bauen:
 * Klick auf den Hamburger-Knopf (Flowbite-Collapse "mobile-menu-2"), dann nach oben, wo das Menü aufgeht.
 */
document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-bottom-nav-more]')) {
        return;
    }
    const toggle = document.querySelector('[data-collapse-toggle="mobile-menu-2"]');
    if (!toggle) {
        return;
    }
    const menu = document.getElementById('mobile-menu-2');
    if (menu?.classList.contains('hidden')) {
        toggle.click();
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
