/**
 * Profil (home/profile.blade.php): Die Ampel-Knöpfe setzen die Klassifizierung per PATCH (Adresse in data-remote)
 * und laden die Seite danach neu.
 */
document.querySelectorAll('.ampel-btn').forEach((button) =>
    button.addEventListener('click', async (event) => {
        event.preventDefault();
        const response = await fetch(button.dataset.remote, {
            method: 'PATCH',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        });
        if (response.ok) {
            window.location.reload();
        }
    }),
);
