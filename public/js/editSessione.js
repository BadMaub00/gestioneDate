document.addEventListener('DOMContentLoaded', () => {
    const dataInizioInput = document.getElementById('data_inizio');
    const dataFineInput = document.getElementById('data_fine');
    const annoInput = document.getElementById('anno');

    function updateAnnoAndMinFine() {
        if (dataInizioInput.value) {
            const inizio = new Date(dataInizioInput.value);

            // Estrae e imposta l'anno accademico dalla data di inizio
            const anno = inizio.getFullYear();
            if (annoInput) {
                annoInput.value = anno;
            }

            // Imposta il limite minimo per la data di fine (+14 giorni)
            inizio.setDate(inizio.getDate() + 14);
            if (dataFineInput) {
                dataFineInput.min = inizio.toISOString().split('T')[0];
            }
        }
    }

    if (dataInizioInput) {
        dataInizioInput.addEventListener('change', updateAnnoAndMinFine);
        updateAnnoAndMinFine(); // Esegue all'apertura della pagina
    }
});