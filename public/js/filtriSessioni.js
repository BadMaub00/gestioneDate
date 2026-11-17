document.addEventListener('DOMContentLoaded', () => {
    const filtroAnno = document.getElementById('filtroAnnoSessione');
    const filtroCorso = document.getElementById('filtroCorsoSessione');
    const btnReset = document.getElementById('btnResetFiltriSessione');
    if (!filtroAnno || !filtroCorso) return;

    const sessionRows = Array.from(document.querySelectorAll('.sessione-row'));
    const noMatchAlert = document.getElementById('noSessioniMatchFilter');
    const tabellaCard = document.getElementById('tabellaSessioniCard');

    function applicaFiltri() {
        const anno = filtroAnno.value;
        const corso = filtroCorso.value;
        let visibleCount = 0;

        sessionRows.forEach(row => {
            const okAnno = anno === '' || row.dataset.anno === anno;
            const okCorso = corso === '' || row.dataset.corso === corso;
            const match = okAnno && okCorso;

            row.classList.toggle('d-none', !match);
            if (match) visibleCount++;
        });

        const nessunRisultato = visibleCount === 0 && sessionRows.length > 0;
        if (noMatchAlert) noMatchAlert.classList.toggle('d-none', !nessunRisultato);
        if (tabellaCard) tabellaCard.classList.toggle('d-none', nessunRisultato);
    }

    filtroAnno.addEventListener('change', applicaFiltri);
    filtroCorso.addEventListener('change', applicaFiltri);

    if (btnReset) {
        btnReset.addEventListener('click', () => {
            filtroCorso.value = '';
            filtroAnno.value = '';
            applicaFiltri();
        });
    }

    applicaFiltri();
});
