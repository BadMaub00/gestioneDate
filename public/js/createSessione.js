document.addEventListener('DOMContentLoaded', () => {
    const dataInizioInput = document.getElementById('data_inizio');
    const dataFineInput = document.getElementById('data_fine');
    const annoInput = document.getElementById('anno');
    const scadenzaInput = document.getElementById('scadenza_inserimento');

    function updateAnnoAndMinFine() {
        if (dataInizioInput.value) {
            // La scadenza per l'inserimento degli appelli non può superare l'inizio della sessione
            if (scadenzaInput) {
                scadenzaInput.max = dataInizioInput.value;
            }

            const inizio = new Date(dataInizioInput.value);

            // Estrae e imposta l'anno accademico dalla data di inizio
            const anno = inizio.getFullYear();
            if (annoInput) {
                annoInput.value = anno;
            }

            // Imposta il limite minimo per la data di fine (+14 giorni)
            inizio.setDate(inizio.getDate() + 14);
            const minFine = inizio.toISOString().split('T')[0];
            if (dataFineInput) {
                dataFineInput.min = minFine;
                if (dataFineInput.value && dataFineInput.value < minFine) {
                    dataFineInput.value = minFine;
                }
            }
        }
    }

    if (dataInizioInput) {
        dataInizioInput.addEventListener('change', updateAnnoAndMinFine);
        if (dataInizioInput.value) {
            updateAnnoAndMinFine();
        }
    }
});