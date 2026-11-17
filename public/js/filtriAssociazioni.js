document.addEventListener('DOMContentLoaded', () => {
    const selectCorso = document.getElementById('filtro_corso_id');
    const selectInsegnamento = document.getElementById('filtro_ins_corso_id');
    const selectDocente = document.getElementById('filtro_docente_id');
    const btnReset = document.getElementById('btnResetFiltriAssociazioni');
    const noMatchAlert = document.getElementById('noAssociazioniMatchFilter');
    const tabellaCard = document.getElementById('tabellaAssociazioniCard');

    if (!selectCorso || !selectInsegnamento || !selectDocente) return;

    const righe = Array.from(document.querySelectorAll('.riga-associazione'));
    const headers = Array.from(document.querySelectorAll('.gruppo-corso-header'));

    // Copia di tutte le opzioni insegnamento presenti nel DOM (escluso il placeholder)
    const insegnamentoOptions = Array.from(selectInsegnamento.querySelectorAll('option[data-corso-id]'));

    // 1. Select a cascata: gli insegnamenti dipendono dal corso scelto
    function filterInsegnamenti() {
        const corsoId = selectCorso.value;

        if (!corsoId) {
            selectInsegnamento.innerHTML = '<option value="">-- Seleziona prima un Corso --</option>';
            selectInsegnamento.value = '';
            selectInsegnamento.disabled = true;
            return;
        }

        const filtered = insegnamentoOptions.filter(opt => opt.getAttribute('data-corso-id') === corsoId);

        selectInsegnamento.innerHTML = filtered.length > 0
            ? '<option value="">-- Tutti gli insegnamenti del corso --</option>'
            : '<option value="">Nessun insegnamento per questo corso</option>';
        filtered.forEach(opt => selectInsegnamento.appendChild(opt.cloneNode(true)));
        selectInsegnamento.value = '';
        selectInsegnamento.disabled = filtered.length === 0;
    }

    // 2. Filtraggio istantaneo delle righe
    function applicaFiltri() {
        const corsoId = selectCorso.value;
        const insCorsoId = selectInsegnamento.value;
        const docenteId = selectDocente.value;

        let totaleVisibili = 0;

        righe.forEach(riga => {
            const match =
                (!corsoId || riga.dataset.corsoId === corsoId) &&
                (!insCorsoId || riga.dataset.insCorsoId === insCorsoId) &&
                (!docenteId || riga.dataset.docenteId === docenteId);

            riga.classList.toggle('d-none', !match);
            if (match) totaleVisibili++;
        });

        // Un'intestazione di gruppo è visibile solo se ha almeno una riga visibile
        // (si scorrono le righe successive fino all'intestazione seguente)
        headers.forEach(header => {
            let visibili = 0;
            let el = header.nextElementSibling;
            while (el && !el.classList.contains('gruppo-corso-header')) {
                if (el.classList.contains('riga-associazione') && !el.classList.contains('d-none')) visibili++;
                el = el.nextElementSibling;
            }
            header.classList.toggle('d-none', visibili === 0);
        });

        const haRighe = righe.length > 0;
        if (noMatchAlert) noMatchAlert.classList.toggle('d-none', totaleVisibili > 0 || !haRighe);
        if (tabellaCard) tabellaCard.classList.toggle('d-none', totaleVisibili === 0 && haRighe);
    }

    selectCorso.addEventListener('change', () => {
        filterInsegnamenti();
        applicaFiltri();
    });
    selectInsegnamento.addEventListener('change', applicaFiltri);
    selectDocente.addEventListener('change', applicaFiltri);

    if (btnReset) {
        btnReset.addEventListener('click', () => {
            selectCorso.value = '';
            selectDocente.value = '';
            filterInsegnamenti();
            applicaFiltri();
        });
    }

    // Stato iniziale
    filterInsegnamenti();
    applicaFiltri();
});
