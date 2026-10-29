/**
 * Fenster "Neue Rückmeldung" / "Rückmeldung bearbeiten" (home/feedback_modal.blade.php) auf Rückmeldungen und Profil.
 * Öffnen über #new-feedback-btn (leer), .edit-post-btn (Daten aus data-post) oder ?neu in der Adresse (Menü unten,
 * Karten der Übersicht); data-open-on-load, wenn die Seite eine bestehende Rückmeldung zum Bearbeiten zeigt.
 * Escape schliesst, ausser die Auswahl der Textbausteine (#default-modal) ist offen.
 */
export function initFeedbackModal(root = document) {
    const modal = root.getElementById('feedback-modal');
    if (!modal) {
        return null;
    }
    const title = root.getElementById('feedback-modal-title');
    const standardTextModal = root.getElementById('default-modal');
    const field = (selector) => modal.querySelector(selector);
    const postId = field('input[name="post_id"]');
    const comment = field('textarea[name="comment"]');
    const showOnSurvey = field('input[name="show_on_survey"]');
    const file = field('input[name="file"]');
    const deleteFileContainer = field('#delete-file-container');
    const deleteFile = field('input[name="delete_file"]');
    const deleteFileLabel = field('label[for="delete_file"]');
    const submit = field('button[type="submit"]');

    const open = () => modal.classList.remove('hidden');
    const close = () => modal.classList.add('hidden');

    function fill(post) {
        postId.value = post?.id ?? '';
        comment.value = post?.comment ?? '';
        showOnSurvey.checked = Boolean(post?.showOnSurvey);
        file.value = '';
        deleteFileContainer?.classList.toggle('hidden', !post?.file);
        if (deleteFileLabel && post?.file) {
            deleteFileLabel.textContent = `Bestehende Datei ${post.filename} löschen`;
        }
        if (deleteFile) {
            deleteFile.checked = false;
        }
        submit.textContent = post ? 'Änderungen speichern' : 'Rückmeldung erstellen';
        title.textContent = post ? 'Rückmeldung bearbeiten' : 'Neue Rückmeldung erstellen';
    }

    function openNew() {
        fill(null);
        open();
        comment.focus();
    }

    root.getElementById('new-feedback-btn')?.addEventListener('click', openNew);

    root.addEventListener('click', (event) => {
        const editButton = event.target.closest('.edit-post-btn');
        if (editButton) {
            event.preventDefault();
            fill(JSON.parse(editButton.dataset.post));
            open();
        }
    });

    root.getElementById('feedback-modal-close').addEventListener('click', close);
    root.getElementById('feedback-modal-backdrop').addEventListener('click', close);

    root.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || modal.classList.contains('hidden')) {
            return;
        }
        if (standardTextModal && standardTextModal.style.display === 'flex') {
            return;
        }
        close();
    });

    if (modal.dataset.openOnLoad === 'true') {
        open();
    } else if (new URLSearchParams(window.location.search).has('neu')) {
        openNew();
    }

    return { open, close, openNew };
}

initFeedbackModal();
