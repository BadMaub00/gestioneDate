<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Calendario Generale Appelli" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="py-3">
            <div class="container-fluid px-md-4 px-lg-5">
                
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                            <i class="bi bi-calendar3 me-2"></i>Calendario Generale
                        </h1>
                        <p class="text-muted mb-0 fw-medium">
                            <strong>Corso:</strong> {{ $corso->nome ?? '' }} | 
                            <strong>Anno:</strong> {{ $annoCorso }}° Anno | 
                            <strong>Sessione:</strong> {{ $sessione->nome ?? '' }} ({{ $sessione->anno ?? '' }})
                        </p>
                    </div>

                    <a href="{{ route('docente.selezionaFiltriGenerale') }}" class="btn btn-outline-unibs fw-bold rounded-3 shadow-sm px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Cambia Filtri
                    </a>
                </div>

                <div class="mb-3 d-flex align-items-center gap-3 fs-7">
                    <span class="fw-bold text-dark">Legenda:</span>
                    <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 rounded-2 fw-semibold">
                        <i class="bi bi-star-fill text-warning me-1"></i> Tuo insegnamento
                    </span>
                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2 fw-normal">
                        Altri insegnamenti
                    </span>
                </div>

                <div class="row g-4 align-items-start">
                    
                    <!-- VISTA GRIGLIA CALENDARIO -->
                    <div class="col-12 col-xl-7 col-xxl-8 order-2 order-xl-1">
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="min-height: 62px;">
                                <button id="btn-prev-week" class="btn btn-outline-light btn-sm fw-bold px-3 py-1 rounded-2">
                                    <i class="bi bi-chevron-left me-1"></i> Settimana Precedente
                                </button>

                                <span id="label-current-week" class="fw-bold text-white fs-5 mb-0">
                                    Caricamento settimane...
                                </span>

                                <button id="btn-next-week" class="btn btn-outline-light btn-sm fw-bold px-3 py-1 rounded-2">
                                    Settimana Successiva <i class="bi bi-chevron-right ms-1"></i>
                                </button>
                            </div>

                            <div class="card-body p-0">
                                <div id="calendar-container" 
                                     data-appelli='@json($appelliAnnoCorso)' 
                                     data-inizio="{{ $sessione->data_inizio }}" 
                                     data-fine="{{ $sessione->data_fine }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABELLA RIASSUNTIVA (Colonna Info Unificata) -->
                    <div class="col-12 col-xl-5 col-xxl-4 order-1 order-xl-2">
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white px-4 border-0 d-flex align-items-center" style="min-height: 62px;">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-list-check me-2 fs-5"></i> Elenco Appelli Programmati
                                </h5>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 680px; overflow-y: auto;">
                                    <table class="table table-hover table-striped align-middle mb-0">
                                        <thead class="table-light border-bottom sticky-top">
                                            <tr>
                                                <th class="ps-3 py-3 text-unibs" style="width: 25%;">Data</th>
                                                <th class="py-3 text-unibs" style="width: 25%;">Orario</th>
                                                <th class="pe-3 py-3 text-unibs" style="width: 50%;">Info</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($appelliAnnoCorso as $appello)
                                                @php
                                                    $isMio = is_object($appello) ? $appello->is_mio_appello : $appello['is_mio_appello'];
                                                    $data = is_object($appello) ? $appello->data : $appello['data'];
                                                    $oraInizio = is_object($appello) ? $appello->ora_inizio : $appello['ora_inizio'];
                                                    $oraFine = is_object($appello) ? $appello->ora_fine : $appello['ora_fine'];
                                                    $nomeIns = is_object($appello) ? $appello->nome_insegnamento : $appello['nome_insegnamento'];
                                                    $docNome = is_object($appello) ? $appello->docente_nome : $appello['docente_nome'];
                                                    $docCognome = is_object($appello) ? $appello->docente_cognome : $appello['docente_cognome'];
                                                    $nomeAula = is_object($appello) ? $appello->nome_aula : $appello['nome_aula'];
                                                    $nomeEdificio = is_object($appello) ? $appello->nome_edificio : $appello['nome_edificio'];
                                                @endphp
                                                <tr class="{{ $isMio ? 'table-warning' : '' }}">
                                                    <!-- Data -->
                                                    <td class="ps-3 py-2.5 fw-bold text-dark fs-7">
                                                        <i class="bi bi-calendar-event text-unibs me-1"></i>
                                                        {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                                                    </td>

                                                    <!-- Orario -->
                                                    <td class="py-2.5 text-dark fw-medium fs-7">
                                                        <i class="bi bi-clock me-1 text-muted"></i>
                                                        {{ \Carbon\Carbon::parse($oraInizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($oraFine)->format('H:i') }}
                                                    </td>

                                                    <!-- Info (Insegnamento/Docente/Sede oppure Fascia Assegnata) -->
                                                    <td class="pe-3 py-2.5 text-dark">
                                                        @if ($isMio)
                                                            <div class="fw-semibold text-dark d-flex align-items-center justify-content-between pe-1">
                                                                <span>{{ $nomeIns }}</span>
                                                                <i class="bi bi-star-fill text-warning ms-1" title="Tuo insegnamento"></i>
                                                            </div>
                                                            <div class="small text-muted fst-italic">
                                                                @if ($docNome && $docCognome)
                                                                    Prof. {{ $docNome }} {{ $docCognome }}
                                                                @else
                                                                    Non assegnato
                                                                @endif
                                                            </div>
                                                            <div class="small fw-semibold text-unibs mt-1">
                                                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $nomeAula ?? 'Aula N/D' }}
                                                                <span class="fw-normal text-muted">({{ $nomeEdificio ?? 'Edificio N/D' }})</span>
                                                            </div>
                                                        @else
                                                            <span class="fst-italic text-muted fw-medium">fascia oraria assegnata</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted fw-bold">
                                                        <i class="bi bi-info-circle fs-3 d-block mb-2 text-secondary"></i>
                                                        Nessun appello trovato.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <x-back-dashboard />

            </div>
        </div>

        <script src="{{ asset('js/calendarioGenerale.js') }}?v={{ time() }}"></script>
    </body>
</html>