document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;

    const appelli = JSON.parse(calendarContainer.dataset.appelli || '[]');
    const sessioneInizio = new Date(calendarContainer.dataset.inizio);
    const sessioneFine = new Date(calendarContainer.dataset.fine);

    const prevBtn = document.getElementById('btn-prev-week');
    const nextBtn = document.getElementById('btn-next-week');
    const labelWeek = document.getElementById('label-current-week');

    function getMonday(d) {
        const date = new Date(d);
        const day = date.getDay();
        const diff = date.getDate() - day + (day === 0 ? -6 : 1); // 1 = Lunedì
        return new Date(date.setDate(diff));
    }

    let currentWeekStart = getMonday(new Date(sessioneInizio));

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    function formatShortDate(date) {
        return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
    }

    function renderCalendar() {
        const weekDays = [];
        for (let i = 0; i < 6; i++) { // Lunedì -> Sabato
            const day = new Date(currentWeekStart);
            day.setDate(currentWeekStart.getDate() + i);
            weekDays.push(day);
        }

        const weekEnd = weekDays[weekDays.length - 1];

        labelWeek.textContent = `Settimana: ${formatShortDate(currentWeekStart)} - ${formatShortDate(weekEnd)}`;

        const prevWeekStart = new Date(currentWeekStart);
        prevWeekStart.setDate(prevWeekStart.getDate() - 7);
        const nextWeekStart = new Date(currentWeekStart);
        nextWeekStart.setDate(nextWeekStart.getDate() + 7);

        prevBtn.disabled = (currentWeekStart <= getMonday(sessioneInizio));
        nextBtn.disabled = (nextWeekStart > sessioneFine);

        const dayNames = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0 calendar-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;" class="bg-light text-muted fw-bold">Orario</th>`;

        weekDays.forEach((day, index) => {
            const isToday = formatDate(day) === formatDate(new Date());
            const inSession = (day >= sessioneInizio && day <= sessioneFine);

            html += `
                <th style="width: 15%;" class="${isToday ? 'table-primary' : ''} ${!inSession ? 'bg-light text-muted opacity-50' : ''}">
                    <div class="fw-bold">${dayNames[index]}</div>
                    <small class="text-secondary">${formatShortDate(day)}</small>
                </th>`;
        });

        html += `</tr></thead><tbody>`;

        for (let hour = 8; hour <= 18; hour++) {
            const timeSlot = `${String(hour).padStart(2, '0')}:00`;

            html += `<tr>`;
            html += `<td class="fw-bold text-muted bg-light fs-7 align-top py-2">${timeSlot}</td>`;

            weekDays.forEach(day => {
                const dayStr = formatDate(day);

                const matches = appelli.filter(appello => {
                    const appelloDate = appello.data;
                    const appelloHour = parseInt(appello.ora_inizio.split(':')[0], 10);
                    return appelloDate === dayStr && appelloHour === hour;
                });

                html += `<td class="align-top p-1 text-start" style="height: 85px; vertical-align: top;">`;

                if (matches.length > 0) {
                    matches.forEach(app => {
                        const oraInizio = app.ora_inizio.substring(0, 5);
                        const oraFine = app.ora_fine.substring(0, 5);
                        const corsoStr = app.nome_corso || 'Corso non specificato';

                        html += `
                            <div class="card border-0 shadow-sm bg-unibs text-white p-2 mb-1 rounded-2">
                                <div class="fw-bold text-truncate" style="font-size: 0.85rem;" title="${app.nome_insegnamento}">
                                    ${app.nome_insegnamento}
                                </div>
                                <div class="small opacity-75 d-flex align-items-center gap-1 my-0.5">
                                    <i class="bi bi-clock"></i> ${oraInizio} - ${oraFine}
                                </div>
                                <div class="small fst-italic text-truncate opacity-90" style="font-size: 0.75rem;" title="${corsoStr}">
                                    <i class="bi bi-mortarboard"></i> ${corsoStr}
                                </div>
                            </div>`;
                    });
                }

                html += `</td>`;
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