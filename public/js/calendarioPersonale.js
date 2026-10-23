document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;

    const appelli = JSON.parse(calendarContainer.dataset.appelli || '[]');
    
    function parseLocalDate(dateStr) {
        if (!dateStr) return new Date();
        const [year, month, day] = dateStr.split('-').map(Number);
        return new Date(year, month - 1, day);
    }

    const sessioneInizio = parseLocalDate(calendarContainer.dataset.inizio);
    const sessioneFine = parseLocalDate(calendarContainer.dataset.fine);

    const prevBtn = document.getElementById('btn-prev-week');
    const nextBtn = document.getElementById('btn-next-week');
    const labelWeek = document.getElementById('label-current-week');

    function getMonday(d) {
        const date = new Date(d);
        const day = date.getDay();
        const diff = date.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(date.setDate(diff));
    }

    let currentWeekStart = getMonday(sessioneInizio);

    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function formatShortDate(date) {
        return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
    }

    function renderCalendar() {
        const weekDays = [];
        for (let i = 0; i < 5; i++) { // Solo 5 giorni (Lun-Ven)
            const day = new Date(currentWeekStart);
            day.setDate(currentWeekStart.getDate() + i);
            weekDays.push(day);
        }

        const weekEnd = weekDays[weekDays.length - 1];

        labelWeek.textContent = `Settimana: ${formatShortDate(currentWeekStart)} - ${formatShortDate(weekEnd)}`;

        prevBtn.disabled = (currentWeekStart <= getMonday(sessioneInizio));
        nextBtn.disabled = (currentWeekStart >= getMonday(sessioneFine));

        const dayNames = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì'];

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0 calendar-table" style="table-layout: fixed; min-width: 750px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;" class="bg-light text-muted fw-bold">Orario</th>`;

        weekDays.forEach((day, index) => {
            const isToday = formatDate(day) === formatDate(new Date());
            const inSession = (day >= sessioneInizio && day <= sessioneFine);

            html += `
                <th style="width: 18%;" class="${isToday ? 'table-primary' : ''} ${!inSession ? 'bg-light text-muted opacity-50' : ''}">
                    <div class="fw-bold">${dayNames[index]}</div>
                    <small class="text-secondary">${formatShortDate(day)}</small>
                </th>`;
        });

        html += `</tr></thead><tbody>`;

        const occupied = new Set();

        for (let hour = 8; hour <= 18; hour++) {
            const timeSlot = `${String(hour).padStart(2, '0')}:00`;

            html += `<tr>`;
            html += `<td class="fw-bold text-muted bg-light fs-7 align-middle py-2" style="height: 60px;">${timeSlot}</td>`;

            weekDays.forEach(day => {
                const dayStr = formatDate(day);
                const cellKey = `${dayStr}_${hour}`;

                if (occupied.has(cellKey)) {
                    return;
                }

                const matches = appelli.filter(appello => {
                    const appelloDate = appello.data;
                    const appelloHour = parseInt(appello.ora_inizio.split(':')[0], 10);
                    return appelloDate === dayStr && appelloHour === hour;
                });

                if (matches.length > 0) {
                    let maxDuration = 1;

                    matches.forEach(app => {
                        const hInizio = parseInt(app.ora_inizio.split(':')[0], 10);
                        let hFine = parseInt(app.ora_fine.split(':')[0], 10);
                        if (app.ora_fine.includes(':') && parseInt(app.ora_fine.split(':')[1], 10) > 0) {
                            hFine += 1;
                        }
                        const duration = Math.max(1, hFine - hInizio);
                        if (duration > maxDuration) maxDuration = duration;
                    });

                    for (let d = 1; d < maxDuration; d++) {
                        if (hour + d <= 18) {
                            occupied.add(`${dayStr}_${hour + d}`);
                        }
                    }

                    html += `<td rowspan="${maxDuration}" class="align-top p-1 text-start" style="position: relative;">`;

                    matches.forEach(app => {
                        const oraInizio = app.ora_inizio.substring(0, 5);
                        const oraFine = app.ora_fine.substring(0, 5);
                        const corsoStr = app.nome_corso || 'Corso non specificato';
                        const annoStr = app.anno_di_corso ? `Anno ${app.anno_di_corso}` : 'Anno N/D';
                        const aulaStr = app.nome_aula || 'Aula N/D';
                        const edificioStr = app.nome_edificio || 'Edificio N/D';

                        const isCompact = (maxDuration === 1);

                        if (isCompact) {
                            html += `
                                <div class="card border-0 shadow-sm bg-unibs text-white p-1.5 rounded-2 d-flex flex-column justify-content-center overflow-hidden" 
                                     style="position: absolute; top: 4px; bottom: 4px; left: 4px; right: 4px; font-size: 0.72rem; line-height: 1.15;" 
                                     title="${app.nome_insegnamento} - ${aulaStr} (${edificioStr})">
                                    <div>
                                        <div class="fw-bold text-truncate" style="font-size: 0.78rem;" title="${app.nome_insegnamento}">
                                            ${app.nome_insegnamento}
                                        </div>
                                        <div class="opacity-75 text-truncate my-0.5">
                                            <i class="bi bi-clock"></i> ${oraInizio} - ${oraFine}
                                        </div>
                                    </div>
                                    <div class="fw-semibold text-truncate opacity-90" style="font-size: 0.70rem;">
                                        <i class="bi bi-geo-alt-fill me-1"></i><strong>${aulaStr}</strong> <span class="text-white-50">(${edificioStr})</span>
                                    </div>
                                </div>`;
                        } else {
                            html += `
                                <div class="card border-0 shadow-sm bg-unibs text-white p-2 rounded-2 d-flex flex-column justify-content-between overflow-hidden" 
                                     style="position: absolute; top: 4px; bottom: 4px; left: 4px; right: 4px; font-size: 0.80rem; line-height: 1.25;">
                                    <div>
                                        <div class="fw-bold text-truncate" style="font-size: 0.85rem;" title="${app.nome_insegnamento}">
                                            ${app.nome_insegnamento}
                                        </div>
                                        <div class="small opacity-75 d-flex align-items-center gap-1 my-1">
                                            <i class="bi bi-clock"></i> ${oraInizio} - ${oraFine}
                                        </div>
                                        <div class="small fw-bold opacity-100 text-truncate mt-1" title="${aulaStr}">
                                            <i class="bi bi-geo-alt-fill me-1"></i>${aulaStr}
                                        </div>
                                        <div class="small text-white-50 fw-normal text-truncate" style="font-size: 0.72rem;" title="${edificioStr}">
                                            ${edificioStr}
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <div class="small fst-italic text-truncate opacity-90" style="font-size: 0.75rem;" title="${corsoStr}">
                                            <i class="bi bi-mortarboard"></i> ${corsoStr}
                                        </div>
                                        <div class="small fst-italic text-truncate opacity-90" style="font-size: 0.75rem;" title="${annoStr}">
                                            <i class="bi bi-bar-chart-steps"></i> ${annoStr}
                                        </div>
                                    </div>
                                </div>`;
                        }
                    });

                    html += `</td>`;
                } else {
                    html += `<td class="align-top p-1 text-start" style="height: 60px;"></td>`;
                }
            });

            html += `</tr>`;
        }

        html += `</tbody></table></div>`;
        calendarContainer.innerHTML = html;
    }

    prevBtn.addEventListener('click', function () {
        currentWeekStart.setDate(currentWeekStart.getDate() - 7);
        renderCalendar();
    });

    nextBtn.addEventListener('click', function () {
        currentWeekStart.setDate(currentWeekStart.getDate() + 7);
        renderCalendar();
    });

    renderCalendar();
});