document.addEventListener('DOMContentLoaded', () => {
    const corsoSelect = document.getElementById('corso_id');
    const sessioneSelect = document.getElementById('sessione_id');
    const annoSelect = document.getElementById('anno_di_corso');
    const btnReset = document.getElementById('btnResetFiltri');
    const noMatchAlert = document.getElementById('noAppelliMatchFilter');
    const blocchiSessione = document.querySelectorAll('.blocco-sessione');

    // 1. Popolamento dinamico delle opzioni "Anno di Corso"
    if (corsoSelect && annoSelect) {
        const initialAnno = annoSelect.getAttribute('data-initial-value') || annoSelect.value;

        function updateAnnoOptions(mantieniIniziale = false) {
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
                if (mantieniIniziale && i.toString() === initialAnno) {
                    opt.selected = true;
                }
                annoSelect.appendChild(opt);
            }
        }

        corsoSelect.addEventListener('change', () => {
            annoSelect.removeAttribute('data-initial-value');
            updateAnnoOptions(false);
        });

        updateAnnoOptions(true);
    }

    // 2. Filtraggio istantaneo delle card/tabelle del calendario
    if (corsoSelect && sessioneSelect && annoSelect && blocchiSessione.length > 0) {
        function applicaFiltriCalendario() {
            const corsoVal = corsoSelect.value;
            const sessioneVal = sessioneSelect.value;
            const annoVal = annoSelect.value;

            let sessioniVisibili = 0;

            blocchiSessione.forEach(bloccoSess => {
                const matchSessione = sessioneVal === '' || bloccoSess.dataset.sessioneId === sessioneVal;

                if (!matchSessione) {
                    bloccoSess.classList.add('d-none');
                    return;
                }

                let corsiVisibiliInSessione = 0;
                const blocchiCorso = bloccoSess.querySelectorAll('.blocco-corso');

                blocchiCorso.forEach(bloccoCorso => {
                    const matchCorso = corsoVal === '' || bloccoCorso.dataset.corsoId === corsoVal;

                    if (!matchCorso) {
                        bloccoCorso.classList.add('d-none');
                        return;
                    }

                    let righeVisibiliInCorso = 0;
                    const righe = bloccoCorso.querySelectorAll('.riga-anno, .riga-appello');

                    righe.forEach(riga => {
                        const matchAnno = annoVal === '' || riga.dataset.annoCorso === annoVal;
                        riga.classList.toggle('d-none', !matchAnno);
                        if (matchAnno && riga.classList.contains('riga-appello')) {
                            righeVisibiliInCorso++;
                        }
                    });

                    const mostraCorso = righeVisibiliInCorso > 0;
                    bloccoCorso.classList.toggle('d-none', !mostraCorso);
                    if (mostraCorso) corsiVisibiliInSessione++;
                });

                const mostraSessione = corsiVisibiliInSessione > 0;
                bloccoSess.classList.toggle('d-none', !mostraSessione);
                if (mostraSessione) sessioniVisibili++;
            });

            if (noMatchAlert) {
                noMatchAlert.classList.toggle('d-none', sessioniVisibili > 0);
            }
        }

        // Al cambio del corso attendiamo un tick affinché sessioniPerCorso.js
        // e updateAnnoOptions() abbiano già aggiornato le altre due tendine
        corsoSelect.addEventListener('change', () => setTimeout(applicaFiltriCalendario, 0));
        sessioneSelect.addEventListener('change', applicaFiltriCalendario);
        annoSelect.addEventListener('change', applicaFiltriCalendario);

        if (btnReset) {
            btnReset.addEventListener('click', () => {
                corsoSelect.value = '';
                corsoSelect.dispatchEvent(new Event('change'));
            });
        }

        // Applica eventuali filtri preimpostati al caricamento
        setTimeout(applicaFiltriCalendario, 0);
    }
});