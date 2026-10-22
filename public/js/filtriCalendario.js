document.addEventListener('DOMContentLoaded', () => {
    const corsoSelect = document.getElementById('corso_id');
    const annoSelect = document.getElementById('anno_di_corso');

    if (corsoSelect && annoSelect) {
        const initialAnno = annoSelect.getAttribute('data-initial-value') || annoSelect.value;

        function updateAnnoOptions() {
            const selectedOption = corsoSelect.options[corsoSelect.selectedIndex];
            const durata = selectedOption ? parseInt(selectedOption.getAttribute('data-durata')) : 0;

            annoSelect.innerHTML = '';

            if (!corsoSelect.value || isNaN(durata) || durata <= 0) {
                annoSelect.innerHTML = '<option value="">-- Seleziona prima un Corso --</option>';
                annoSelect.disabled = true;
                return;
            }

            annoSelect.disabled = false;
            annoSelect.innerHTML = '<option value="">-- Tutti gli anni --</option>';

            for (let i = 1; i <= durata; i++) {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = `${i}° Anno`;
                if (i.toString() === initialAnno) {
                    opt.selected = true;
                }
                annoSelect.appendChild(opt);
            }
        }

        corsoSelect.addEventListener('change', () => {
            annoSelect.removeAttribute('data-initial-value');
            updateAnnoOptions();
        });

        updateAnnoOptions();
    }
});