document.addEventListener('DOMContentLoaded', () => {
    const selectCorso = document.getElementById('filtro_corso_id');
    const selectInsegnamento = document.getElementById('filtro_ins_corso_id');

    if (selectCorso && selectInsegnamento) {
        // Mantiene una copia di tutte le opzioni insegnamento presenti nel DOM
        const insegnamentoOptions = Array.from(selectInsegnamento.querySelectorAll('option[data-corso-id]'));
        const initialInsCorsoId = selectInsegnamento.getAttribute('data-initial-value') || selectInsegnamento.value;

        function filterInsegnamenti() {
            const corsoId = selectCorso.value;

            if (!corsoId) {
                // Nessun corso selezionato: disabilita il select e imposta il placeholder
                selectInsegnamento.innerHTML = '<option value="">-- Seleziona prima un Corso --</option>';
                selectInsegnamento.value = '';
                selectInsegnamento.disabled = true;
                return;
            }

            // Corso selezionato: abilita e filtra gli insegnamenti pertinenti
            selectInsegnamento.disabled = false;
            selectInsegnamento.innerHTML = '<option value="">-- Tutti gli insegnamenti del corso --</option>';

            const filtered = insegnamentoOptions.filter(opt => opt.getAttribute('data-corso-id') === corsoId);
            
            filtered.forEach(opt => {
                const clone = opt.cloneNode(true);
                if (clone.value === initialInsCorsoId) {
                    clone.selected = true;
                }
                selectInsegnamento.appendChild(clone);
            });
        }

        selectCorso.addEventListener('change', () => {
            // Reset del valore iniziale al cambio manuale del corso
            selectInsegnamento.removeAttribute('data-initial-value');
            filterInsegnamenti();
        });

        // Inizializza lo stato del filtro al caricamento della pagina
        filterInsegnamenti();
    }
});