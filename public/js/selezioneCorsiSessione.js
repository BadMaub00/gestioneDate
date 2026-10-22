document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = () => document.querySelectorAll('.corso-checkbox');
    const tutti = document.getElementById('selezionaTuttiCorsi');
    const nessuno = document.getElementById('deselezionaTuttiCorsi');

    if (tutti) {
        tutti.addEventListener('click', () => checkboxes().forEach(cb => { cb.checked = true; }));
    }
    if (nessuno) {
        nessuno.addEventListener('click', () => checkboxes().forEach(cb => { cb.checked = false; }));
    }

    // Almeno un corso selezionato prima dell'invio
    const form = document.querySelector('form[action*="sessioni"]');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (checkboxes().length > 0 && !Array.from(checkboxes()).some(cb => cb.checked)) {
                e.preventDefault();
                alert('Seleziona almeno un corso di studi.');
            }
        });
    }
});
