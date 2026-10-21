document.addEventListener('DOMContentLoaded', () => {
    const selectEdificio = document.getElementById('edificio_id');
    const selectAula = document.getElementById('aula_id');

    if (selectEdificio && selectAula) {
        const aulaOptions = Array.from(selectAula.querySelectorAll('option[data-edificio-id]'));

        selectEdificio.addEventListener('change', () => {
            const edificioId = selectEdificio.value;
            selectAula.innerHTML = '<option value="" disabled selected>-- Seleziona Aula --</option>';

            if (!edificioId) {
                selectAula.disabled = true;
                return;
            }

            const filtered = aulaOptions.filter(opt => opt.getAttribute('data-edificio-id') === edificioId);
            filtered.forEach(opt => selectAula.appendChild(opt.cloneNode(true)));
            selectAula.disabled = false;
        });
    }
});