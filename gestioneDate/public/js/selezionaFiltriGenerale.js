document.addEventListener('DOMContentLoaded', function () {
    const corsoSelect = document.getElementById('corso_id');
    const annoSelect = document.getElementById('annoCorso');

    if (!corsoSelect || !annoSelect) return;

    corsoSelect.addEventListener('change', function () {
        const corsoId = this.value;
        // Recupera il template dell'URL dall'attributo HTML data-url-template
        const routeTemplate = this.dataset.urlTemplate;

        annoSelect.innerHTML = '<option value="" selected disabled>Caricamento anni in corso...</option>';
        annoSelect.disabled = true;

        if (corsoId && routeTemplate) {
            // Sostituisce il segnaposto :id con l'ID effettivo del corso
            const fetchUrl = routeTemplate.replace(':id', corsoId);

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Risposta di rete non valida');
                    }
                    return response.json();
                })
                .then(anni => {
                    annoSelect.innerHTML = '<option value="" selected disabled>-- Seleziona Anno --</option>';
                    
                    if (anni.length > 0) {
                        anni.forEach(anno => {
                            const option = document.createElement('option');
                            option.value = anno;
                            option.textContent = `${anno}° Anno`;
                            annoSelect.appendChild(option);
                        });
                        annoSelect.disabled = false;
                    } else {
                        annoSelect.innerHTML = '<option value="" selected disabled>Nessun anno trovato per questo corso</option>';
                    }
                })
                .catch(error => {
                    console.error('Errore durante il recupero degli anni:', error);
                    annoSelect.innerHTML = '<option value="" selected disabled>Errore nel caricamento</option>';
                });
        }
    });
});