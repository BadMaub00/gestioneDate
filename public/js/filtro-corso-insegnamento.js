document.addEventListener('DOMContentLoaded', function () {
    const corsoSelect = document.getElementById('corso_id');
    const insegnamentoSelect = document.getElementById('ins_corso_id');

    // Memorizza tutte le opzioni degli insegnamenti presenti al caricamento
    const tutteLeOpzioni = Array.from(insegnamentoSelect.querySelectorAll('option[data-corso-id]'));

    corsoSelect.addEventListener('change', function () {
        const selectedCorsoId = this.value;

        // Reset del select insegnamento
        insegnamentoSelect.innerHTML = '<option value="" disabled selected>-- Seleziona prima un corso --</option>';

        if (selectedCorsoId) {
            // Filtra le opzioni relative al corso selezionato
            const opzioniFiltrate = tutteLeOpzioni.filter(opt => opt.getAttribute('data-corso-id') === selectedCorsoId);

            if (opzioniFiltrate.length > 0) {
                insegnamentoSelect.innerHTML = '<option value="" disabled selected>-- Seleziona un insegnamento --</option>';
                opzioniFiltrate.forEach(option => {
                    insegnamentoSelect.appendChild(option.cloneNode(true));
                });
                insegnamentoSelect.disabled = false;
            } else {
                insegnamentoSelect.innerHTML = '<option value="" disabled selected>Nessun insegnamento disponibile per questo corso</option>';
                insegnamentoSelect.disabled = true;
            }
        } else {
            insegnamentoSelect.disabled = true;
        }
    });
});