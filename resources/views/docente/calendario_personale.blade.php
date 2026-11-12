<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Calendario Personale - Docente" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        @php
            $appelliArray = [];
            foreach($insegnamentiCorsi as $insC) {
                foreach($insC->appelli as $appello) {
                    $appelliArray[] = [
                        'appello_id' => $appello->appello_id,
                        'data' => $appello->data,
                        'ora_inizio' => $appello->ora_inizio,
                        'ora_fine' => $appello->ora_fine,
                        'nome_insegnamento' => $insC->insegnamento->nome,
                        'nome_corso' => $insC->corso->nome,
                        'anno_di_corso' => $insC->anno_di_corso,
                        'ruolo_docente' => $insC->ruolo_docente ?? 'Secondario',
                        'nome_aula' => $appello->aula->nome ?? 'N/D',
                        'nome_edificio' => $appello->aula->edificio->nome ?? 'N/D',
                    ];
                }
            }
        @endphp

        <div class="py-3">
            <div class="container-fluid px-md-4 px-lg-5">
                
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                            <i class="bi bi-calendar-check-fill me-2"></i>Calendario Personale Appelli
                        </h1>
                        <p class="text-muted mb-0 fw-medium">
                            <strong>Docente:</strong> Prof. {{ Auth::user()->nome }} {{ Auth::user()->cognome }} | 
                            <strong>Corso:</strong> {{ $corso->nome ?? '' }} | <strong>Sessione:</strong> {{ $sessione->nome }} ({{ $sessione->anno }})
                        </p>
                    </div>

                    <a href="{{ route('docente.selezionaSessione') }}" class="btn btn-outline-unibs fw-bold rounded-3 shadow-sm px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Cambia Sessione
                    </a>
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
                                     data-appelli='@json($appelliArray)' 
                                     data-inizio="{{ $sessione->data_inizio }}" 
                                     data-fine="{{ $sessione->data_fine }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABELLA RIASSUNTIVA (4 Colonne Separate) -->
                    <div class="col-12 col-xl-5 col-xxl-4 order-1 order-xl-2">
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white px-4 border-0 d-flex justify-content-between align-items-center" style="min-height: 62px;">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-list-check me-2 fs-5"></i> I Miei Appelli
                                </h5>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 680px; overflow-y: auto;">
                                    <table class="table table-hover table-striped align-middle mb-0">
                                        <thead class="table-light border-bottom sticky-top">
                                            <tr>
                                                <th class="ps-3 py-3 text-unibs" style="width: 25%;">Data & Orario</th>
                                                <th class="py-3 text-unibs" style="width: 30%;">Insegnamento</th>
                                                <th class="py-3 text-unibs" style="width: 25%;">Sede</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hasAppelli = false; @endphp
                                            @foreach($insegnamentiCorsi as $insC)
                                                @foreach($insC->appelli as $appello)
                                                    @php $hasAppelli = true; @endphp
                                                    <tr>
                                                        <!-- Data e Orario -->
                                                        <td class="ps-3 py-3 text-dark fs-7">
                                                            <div class="fw-bold">
                                                                <i class="bi bi-calendar-event text-unibs me-1"></i>
                                                                {{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                            </div>
                                                            <div class="small text-muted fw-medium mt-0.5">
                                                                <i class="bi bi-clock me-1"></i>
                                                                {{ \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($appello->ora_fine)->format('H:i') }}
                                                            </div>
                                                        </td>

                                                        <!-- Insegnamento -->
                                                        <td class="py-3 text-dark">
                                                            <div class="fw-semibold text-dark">{{ $insC->insegnamento->nome }}</div>
                                                            <div class="small text-muted fst-italic mt-1">
                                                                <i class="bi bi-mortarboard me-1 text-unibs"></i>{{ $insC->corso->nome }}
                                                            </div>
                                                            <div class="small text-muted fst-italic">
                                                                <i class="bi bi-bar-chart-steps me-1 text-unibs"></i>Anno di corso: {{ $insC->anno_di_corso }}
                                                            </div>
                                                        </td>

                                                        <!-- Sede (Colonna Separata) -->
                                                        <td class="py-3 text-dark">
                                                            <div class="fw-semibold text-dark">
                                                                <i class="bi bi-geo-alt-fill text-unibs me-1"></i>{{ $appello->aula->nome ?? 'Aula N/D' }}
                                                            </div>
                                                            <div class="small text-muted">
                                                                {{ $appello->aula->edificio->nome ?? 'Edificio N/D' }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                            @if(!$hasAppelli)
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted fw-bold">
                                                        <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                                                        Nessun appello fissato per le tue materie in questa sessione.
                                                    </td>
                                                </tr>
                                            @endif
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

        <script src="{{ asset('js/calendarioPersonale.js') }}?v={{ time() }}"></script>
    </body>
</html>