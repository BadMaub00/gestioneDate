document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.getElementById('calendar-container');
    const btnPrevWeek = document.getElementById('btn-prev-week');
    const btnNextWeek = document.getElementById('btn-next-week');
    const labelCurrentWeek = document.getElementById('label-current-week');

    if (!calendarContainer) return;

    const appelli = JSON.parse(calendarContainer.getAttribute('data-appelli') || '[]');
    const dataInizioStr = calendarContainer.getAttribute('data-inizio');
    const dataFineStr = calendarContainer.getAttribute('data-fine');

    if (!dataInizioStr || !dataFineStr) {
        calendarContainer.innerHTML = `<div class="p-4 text-center text-muted">Mancano le date di inizio o fine sessione.</div>`;
        return;
    }

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
        tempEnd.setDate(tempEnd.getDate() + 4);
        
        weeks.push({
            start: new Date(tempStart),
            end: tempEnd
        });

        tempStart.setDate(tempStart.getDate() + 7);
    }

    let currentWeekIndex = 0;
    const dayNames = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì'];

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
        for (let i = 0; i < 5; i++) {
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
                <th style="width: 18%;">
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

        const occupied = new Set();

        for (let hour = 8; hour <= 18; hour++) {
            const timeSlot = `${String(hour).padStart(2, '0')}:00`;

            html += `
                <tr>
                    <td class="fw-bold text-secondary bg-light small py-2 align-middle" style="height: 60px;">${timeSlot}</td>
            `;

            days.forEach(day => {
                const dayYMD = formatDateToYMD(day);
                const cellKey = `${dayYMD}_${hour}`;

                if (occupied.has(cellKey)) {
                    return;
                }

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

                    for (let d = 1; d < maxDuration; d++) {
                        if (hour + d <= 18) {
                            occupied.add(`${dayYMD}_${hour + d}`);
                        }
                    }

                    html += `<td rowspan="${maxDuration}" class="p-1 align-top" style="position: relative;">`;

                    appelliSlot.forEach(appello => {
                        const oraInizio = appello.ora_inizio.substring(0, 5);
                        const oraFine = appello.ora_fine.substring(0, 5);
                        const docente = appello.docente_nome 
                            ? `Prof. ${appello.docente_nome} ${appello.docente_cognome}` 
                            : 'Non assegnato';
                        
                        const aulaStr = appello.nome_aula || 'Aula N/D';
                        const edificioStr = appello.nome_edificio || 'Edificio N/D';

                        const isCompact = (maxDuration === 1);

                        if (appello.is_mio_appello) {
                            if (isCompact) {
                                html += `
                                    <div class="p-1.5 text-start rounded-2 shadow-sm bg-warning-subtle border border-warning text-dark d-flex flex-column justify-content-center overflow-hidden" 
                                         style="position: absolute; top: 4px; bottom: 4px; left: 4px; right: 4px; font-size: 0.72rem; line-height: 1.15;" 
                                         title="${appello.nome_insegnamento} - ${aulaStr} (${edificioStr})">
                                        <div class="fw-bold text-truncate d-flex justify-content-between align-items-center">
                                            <span class="text-truncate">${appello.nome_insegnamento}</span>
                                            <i class="bi bi-star-fill text-warning ms-1" style="font-size: 0.65rem;"></i>
                                        </div>
                                        <div class="opacity-90 text-truncate"><i class="bi bi-clock me-1 text-muted"></i>${oraInizio} - ${oraFine}</div>
                                        <div class="text-truncate fw-bold text-unibs mt-0.5" style="font-size: 0.70rem;">
                                            <i class="bi bi-geo-alt-fill me-1"></i>${aulaStr} <span class="fw-normal text-muted">(${edificioStr})</span>
                                        </div>
                                    </div>
                                `;
                            } else {
                                html += `
                                    <div class="p-2 text-start rounded-3 shadow-sm bg-warning-subtle border border-warning text-dark d-flex flex-column justify-content-between overflow-hidden" 
                                         style="position: absolute; top: 4px; bottom: 4px; left: 4px; right: 4px; font-size: 0.80rem; line-height: 1.25;">
                                        <div>
                                            <div class="fw-bold mb-1 d-flex justify-content-between align-items-center text-truncate">
                                                <span class="text-truncate">${appello.nome_insegnamento}</span>
                                                <i class="bi bi-star-fill text-warning ms-1" title="Tuo insegnamento"></i>
                                            </div>
                                            <div class="small text-dark fw-medium opacity-90"><i class="bi bi-clock me-1 text-muted"></i>${oraInizio} - ${oraFine}</div>
                                            <div class="small fw-bold text-unibs text-truncate mt-1" title="${aulaStr}">
                                                <i class="bi bi-geo-alt-fill me-1"></i>${aulaStr}
                                            </div>
                                            <div class="small text-muted fw-normal text-truncate" style="font-size: 0.72rem;" title="${edificioStr}">
                                                ${edificioStr}
                                            </div>
                                        </div>
                                        <div class="small text-muted fst-italic text-truncate mt-1"><i class="bi bi-person me-1"></i>${docente}</div>
                                    </div>
                                `;
                            }
                        } else {
                            // APPELLO NON DEL DOCENTE: Solamente la scritta centrata
                            html += `
                                <div class="p-2 text-center rounded-3 shadow-sm text-white d-flex align-items-center justify-content-center overflow-hidden" 
                                     style="position: absolute; top: 4px; bottom: 4px; left: 4px; right: 4px; background-color: #002b49; font-size: 0.75rem; line-height: 1.2;" 
                                     title="Fascia oraria assegnata">
                                    <span class="fw-semibold">Fascia oraria assegnata</span>
                                </div>
                            `;
                        }
                    });

                    html += `</td>`;
                } else {
                    html += `<td class="p-1 align-top" style="height: 60px;"></td>`;
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