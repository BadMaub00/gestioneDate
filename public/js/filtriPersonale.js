document.addEventListener('DOMContentLoaded', () => {
    const filtroRuolo = document.getElementById('filtro_ruolo');
    const filtroCognome = document.getElementById('filtro_cognome');
    const filtroNome = document.getElementById('filtro_nome');
    const btnReset = document.getElementById('btnResetFiltriPersonale');
    const noMatchAlert = document.getElementById('noPersonaleMatchFilter');
    const tabellaCard = document.getElementById('tabellaPersonaleCard');

    if (!filtroRuolo || !filtroCognome || !filtroNome) return;

    const righe = Array.from(document.querySelectorAll('.riga-personale'));

    function applicaFiltri() {
        const ruolo = filtroRuolo.value;
        const cognome = filtroCognome.value;
        const nome = filtroNome.value;
        let visibili = 0;

        righe.forEach(riga => {
            const match =
                (ruolo === '' || riga.dataset.ruolo === ruolo) &&
                (cognome === '' || riga.dataset.cognome === cognome) &&
                (nome === '' || riga.dataset.nome === nome);

            riga.classList.toggle('d-none', !match);
            if (match) visibili++;
        });

        const nessunRisultato = visibili === 0 && righe.length > 0;
        if (noMatchAlert) noMatchAlert.classList.toggle('d-none', !nessunRisultato);
        if (tabellaCard) tabellaCard.classList.toggle('d-none', nessunRisultato);
    }

    [filtroRuolo, filtroCognome, filtroNome].forEach(sel => sel.addEventListener('change', applicaFiltri));

    if (btnReset) {
        btnReset.addEventListener('click', () => {
            filtroRuolo.value = '';
            filtroCognome.value = '';
            filtroNome.value = '';
            applicaFiltri();
        });
    }

    applicaFiltri();
});
