document.addEventListener('DOMContentLoaded', function () {
    const edificioSelect = document.getElementById('edificio_id');
    const aulaSelect = document.getElementById('aula_id');

    if (edificioSelect && aulaSelect) {
        const aulaOptions = Array.from(aulaSelect.querySelectorAll('option[data-edificio-id]'));
        const selectedOption = aulaSelect.querySelector('option[selected]');
        const currentAulaId = selectedOption ? selectedOption.value : aulaSelect.value;

        function filterAule() {
            const selectedEdificioId = edificioSelect.value;

            aulaSelect.innerHTML = '<option value="" disabled>-- Seleziona Aula --</option>';

            if (!selectedEdificioId) {
                aulaSelect.disabled = true;
                return;
            }

            const filteredAule = aulaOptions.filter(opt => opt.getAttribute('data-edificio-id') === selectedEdificioId);

            filteredAule.forEach(opt => {
                const newOpt = opt.cloneNode(true);
                if (newOpt.value === currentAulaId) {
                    newOpt.selected = true;
                }
                aulaSelect.appendChild(newOpt);
            });

            aulaSelect.disabled = false;
        }

        edificioSelect.addEventListener('change', filterAule);
        filterAule();
    }
});