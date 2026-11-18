/**
 * Startseite (welcome.blade.php): Die Zähler für Kurse und Qualifikationen zählen hoch, sobald sie ins Bild kommen.
 * Ersetzt jQuery Waypoints und custom.js aus dem alten Template; ausgelöst wie dort, wenn der Block 75 % der
 * Fensterhöhe erreicht.
 */
function countUp(counter, steps) {
    const target = Number(counter.dataset.counter);
    let step = 0;
    const tick = () => {
        step++;
        counter.textContent = String(Math.trunc((target * step) / steps));
        if (step < steps) {
            setTimeout(tick, 1);
        }
    };
    tick();
}

const container = document.getElementById('counterUp');
if (container) {
    const observer = new IntersectionObserver(
        (entries) => {
            if (!entries.some((entry) => entry.isIntersecting)) {
                return;
            }
            observer.disconnect();
            container.querySelectorAll('.counter').forEach((counter) => countUp(counter, 5));
        },
        { rootMargin: '0px 0px -25% 0px' },
    );
    observer.observe(container);
}
