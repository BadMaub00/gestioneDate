document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.getElementById('calendar-container');
    const btnPrevWeek = document.getElementById('btn-prev-week');
    const btnNextWeek = document.getElementById('btn-next-week');
    const labelCurrentWeek = document.getElementById('label-current-week');

    if (!calendarContainer) return;

    // 1. Estrazione dati dagli attributi HTML
    const appelli = JSON.parse(calendarContainer.getAttribute('data-appelli') || '[]');
    const dataInizioStr = calendarContainer.getAttribute('data-inizio');
    const dataFineStr = calendarContainer.getAttribute('data-fine');

    if (!dataInizioStr || !dataFineStr) {
        calendarContainer.innerHTML = `<div class="p-4 text-center text-muted">Mancano le date di inizio o fine sessione.</div>`;
        return;
    }

    // Helper per conversione date
    function parseLocalDate(dateStr) {
        const [year, month, day] = dateStr.split('-').map(Number);
        return new Date(year, month - 1, day);
    }

    function formatDateToYMD(dateObj) {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // 2. Calcolo delle settimane da Lunedì a Sabato (6 giorni)
    const startDateSession = parseLocalDate(dataInizioStr);
    const endDateSession = parseLocalDate(dataFineStr);

    function getMonday(d) {
        const date = new Date(d);
        const day = date.getDay();
        const diff = date.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(date.setDate(diff));
    }

    let currentWeekStart = getMonday(startDateSession);
    const weeks = [];

    let tempStart = new Date(currentWeekStart);
    while (tempStart <= endDateSession) {
        const tempEnd = new Date(tempStart);
        tempEnd.setDate(tempEnd.getDate() + 5); // Lunedì -> Sabato
        
        weeks.push({
            start: new Date(tempStart),
            end: tempEnd
        });

        tempStart.setDate(tempStart.getDate() + 7);
    }

    let currentWeekIndex = 0;
    const dayNames = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];

    // 3. Renderizzazione del Calendario con rowspan
    function renderWeek(index) {
        if (weeks.length === 0) {
            calendarContainer.innerHTML = `<div class="p-4 text-center text-muted">Nessuna settimana trovata per questa sessione.</div>`;
            return;
        }

        const week = weeks[index];
        
        const formatDM = (d) => {
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            return `${day}/${month}`;
        };

        labelCurrentWeek.textContent = `Settimana: ${formatDM(week.start)} - ${formatDM(week.end)}`;

        btnPrevWeek.disabled = (index === 0);
        btnNextWeek.disabled = (index === weeks.length - 1);

        const days = [];
        for (let i = 0; i < 6; i++) {
            const d = new Date(week.start);
            d.setDate(d.getDate() + i);
            days.push(d);
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0" style="min-width: 750px; table-layout: fixed;">
                    <thead class="table-light align-middle border-bottom">
                        <tr>
                            <th style="width: 10%; color: #6c757d;" class="fw-bold">Orario</th>
        `;

        days.forEach((day, idx) => {
            html += `
                <th style="width: 15%;">
                    <div class="fw-bold text-dark">${dayNames[idx]}</div>
                    <div class="small text-muted fw-normal">${formatDM(day)}</div>
                </th>
            `;
        });

        html += `
                        </tr>
                    </thead>
                    <tbody>
        `;

        // Tracciamento celle coperte da rowspan
        const occupied = new Set();

        // Ciclo dalle 08:00 alle 18:00 (lo slot 18:00 copre fino alle 19:00)
        for (let hour = 8; hour <= 18; hour++) {
            const timeSlot = `${String(hour).padStart(2, '0')}:00`;

            html += `
                <tr>
                    <td class="fw-bold text-secondary bg-light small py-2 align-middle">${timeSlot}</td>
            `;

            days.forEach(day => {
                const dayYMD = formatDateToYMD(day);
                const cellKey = `${dayYMD}_${hour}`;

                // Se la cella è già coperta da un rowspan precedente, salta la creazione del td
                if (occupied.has(cellKey)) {
                    return;
                }

                // Trova gli appelli che iniziano in questa ora
                const appelliSlot = appelli.filter(app => {
                    if (app.data !== dayYMD) return false;
                    const oraInizioHH = parseInt(app.ora_inizio.split(':')[0], 10);
                    return oraInizioHH === hour;
                });

                if (appelliSlot.length > 0) {
                    let maxDuration = 1;

                    appelliSlot.forEach(appello => {
                        const hInizio = parseInt(appello.ora_inizio.split(':')[0], 10);
                        let hFine = parseInt(appello.ora_fine.split(':')[0], 10);
                        if (appello.ora_fine.includes(':') && parseInt(appello.ora_fine.split(':')[1], 10) > 0) {
                            hFine += 1;
                        }
                        const duration = Math.max(1, hFine - hInizio);
                        if (duration > maxDuration) maxDuration = duration;
                    });

                    // Segna le slot ore successive come occupate
                    for (let d = 1; d < maxDuration; d++) {
                        if (hour + d <= 18) {
                            occupied.add(`${dayYMD}_${hour + d}`);
                        }
                    }

                    html += `<td rowspan="${maxDuration}" class="p-1 align-top" style="height: 1px;">`;

                    appelliSlot.forEach(appello => {
                        const oraInizio = appello.ora_inizio.substring(0, 5);
                        const oraFine = appello.ora_fine.substring(0, 5);
                        const docente = appello.docente_nome 
                            ? `Prof. ${appello.docente_nome} ${appello.docente_cognome}` 
                            : 'Non assegnato';

                        if (appello.is_mio_appello) {
                            html += `
                                <div class="p-2 text-start rounded-3 shadow-sm bg-warning-subtle border border-warning text-dark h-100 d-flex flex-column justify-content-between mb-1" 
                                     style="font-size: 0.82rem; line-height: 1.3;">
                                    <div>
                                        <div class="fw-bold mb-1 d-flex justify-content-between align-items-center">
                                            <span>${appello.nome_insegnamento}</span>
                                            <i class="bi bi-star-fill text-warning ms-1" title="Tuo insegnamento"></i>
                                        </div>
                                        <div class="small text-dark fw-medium opacity-90"><i class="bi bi-clock me-1 text-muted"></i>${oraInizio} - ${oraFine}</div>
                                    </div>
                                    <div class="small text-muted fst-italic mt-1"><i class="bi bi-person me-1"></i>${docente}</div>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="p-2 text-start rounded-3 shadow-sm text-white h-100 d-flex flex-column justify-content-between mb-1" 
                                     style="background-color: #002b49; font-size: 0.82rem; line-height: 1.3;">
                                    <div>
                                        <div class="fw-bold mb-1">${appello.nome_insegnamento}</div>
                                        <div class="small opacity-90"><i class="bi bi-clock me-1"></i>${oraInizio} - ${oraFine}</div>
                                    </div>
                                    <div class="small opacity-90 fst-italic mt-1"><i class="bi bi-person me-1"></i>${docente}</div>
                                </div>
                            `;
                        }
                    });

                    html += `</td>`;
                } else {
                    html += `<td class="p-1 align-top" style="height: 65px;"></td>`;
                }
            });

            html += `</tr>`;
        }

        html += `
                    </tbody>
                </table>
            </div>
        `;

        calendarContainer.innerHTML = html;
    }

    // 4. Navigazione tra le settimane
    btnPrevWeek.addEventListener('click', () => {
        if (currentWeekIndex > 0) {
            currentWeekIndex--;
            renderWeek(currentWeekIndex);
        }
    });

    btnNextWeek.addEventListener('click', () => {
        if (currentWeekIndex < weeks.length - 1) {
            currentWeekIndex++;
            renderWeek(currentWeekIndex);
        }
    });

    renderWeek(currentWeekIndex);
});