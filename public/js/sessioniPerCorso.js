/**
 * Select a cascata: la scelta della sessione dipende dal corso.
 * Richiede due <select> con id "corso_id" e "sessione_id"; ogni <option> della sessione
 * ha l'attributo data-corso con l'id del corso a cui appartiene.
 * Attributi opzionali su #sessione_id:
 *   data-testo-vuoto  testo del placeholder finché non si sceglie un corso
 *   data-testo-attivo testo del placeholder dopo aver scelto un corso
 */
document.addEventListener('DOMContentLoaded', () => {
    const corsoSelect = document.getElementById('corso_id');
    const sessioneSelect = document.getElementById('sessione_id');

    if (!corsoSelect || !sessioneSelect) return;

    const placeholder = sessioneSelect.querySelector('option[value=""]');
    const testoVuoto = sessioneSelect.dataset.testoVuoto || '-- Seleziona prima un Corso --';
    const testoAttivo = sessioneSelect.dataset.testoAttivo || '-- Seleziona Sessione --';
    const opzioni = Array.from(sessioneSelect.querySelectorAll('option[data-corso]'));

    function aggiorna(mantieniSelezione) {
        const corso = corsoSelect.value;
        const valorePrecedente = sessioneSelect.value;
        let disponibili = 0;

        opzioni.forEach(opt => {
            const visibile = corso !== '' && opt.dataset.corso === corso;
            opt.hidden = !visibile;
            opt.disabled = !visibile;
            if (visibile) disponibili++;
        });

        if (placeholder) {
            placeholder.textContent = corso === ''
                ? testoVuoto
                : (disponibili > 0 ? testoAttivo : 'Nessuna sessione per questo corso');
        }

        const ancoraValida = mantieniSelezione && opzioni.some(o => o.value === valorePrecedente && !o.disabled);
        sessioneSelect.value = ancoraValida ? valorePrecedente : '';
        sessioneSelect.disabled = corso === '' || disponibili === 0;
    }

    corsoSelect.addEventListener('change', () => aggiorna(false));
    aggiorna(true);
});
