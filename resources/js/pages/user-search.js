/**
 * Person hinzufügen (admin/users/create.blade.php): Vorschläge beim Tippen des Namens über ein natives <datalist>.
 * Wählt man einen Vorschlag, steht der Name im Feld und die ID im versteckten Feld user_id. Ersetzt jQuery UI
 * Autocomplete.
 */
const MIN_LENGTH = 3;
const DELAY = 250;

function init(input) {
    const list = document.getElementById(input.getAttribute('list'));
    const userId = input.form.elements.user_id;
    // Text des Vorschlags (Name - E-Mail) -> Person
    let users = new Map();
    let timer;
    let request = 0;

    function show(found) {
        users = new Map(found.map((user) => [`${user.username} - ${user.email}`, user]));
        list.replaceChildren(
            ...[...users.keys()].map((label) => {
                const option = document.createElement('option');
                option.value = label;
                return option;
            }),
        );
    }

    async function search(term) {
        const current = ++request;
        try {
            const response = await fetch(`${input.dataset.searchUrl}?${new URLSearchParams({ term })}`, {
                headers: { Accept: 'application/json' },
            });
            // Nur die Antwort auf die letzte Eingabe zeigen.
            if (response.ok && current === request) {
                show(await response.json());
            }
        } catch {
            // Ohne Vorschläge lässt sich der Name weiterhin von Hand eingeben.
        }
    }

    input.addEventListener('input', () => {
        const user = users.get(input.value);
        clearTimeout(timer);
        // Laufende Suchen gelten nicht mehr.
        request++;
        if (user) {
            input.value = user.username;
            userId.value = String(user.id);
            show([]);
            return;
        }
        userId.value = '';
        const term = input.value.trim();
        if (term.length < MIN_LENGTH) {
            show([]);
            return;
        }
        timer = setTimeout(() => search(term), DELAY);
    });
}

document.querySelectorAll('input[data-search-url]').forEach(init);
