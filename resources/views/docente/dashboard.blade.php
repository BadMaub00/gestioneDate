<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Dashboard Docente" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="py-3">
            <div class="container px-md-4 px-lg-5">
                
                <!-- Intestazione Profilo Docente -->
                <div class="mb-4 border-bottom border-2 border-unibs pb-2">
                    <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                        <i class="bi bi-journal-bookmark-fill me-2 fs-3"></i>
                        Docente: Prof. {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                    </h1>
                </div>
                
                <!-- Link Calendari -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <a href="{{ route('docente.selezionaSessione') }}" 
                           class="btn btn-white text-unibs bg-white border-0 fw-bold py-3 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-check-fill me-2 fs-5"></i> CALENDARIO PERSONALE
                        </a>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('docente.selezionaFiltriGenerale') }}" 
                           class="btn btn-white text-unibs bg-white border-0 fw-bold py-3 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar3 me-2 fs-5"></i> CALENDARIO GENERALE
                        </a>
                    </div>
                </div>

                <!-- Card Tabella Insegnamenti -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                    <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                            <i class="bi bi-journal-text me-2 fs-5"></i> Gestione Insegnamenti e Appelli
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="py-3 ps-4 text-unibs fw-bold" style="width: 32%;">Insegnamento</th>
                                        <th class="py-3 text-unibs fw-bold" style="width: 28%;">Corso di Studi</th>
                                        <th class="py-3 text-unibs fw-bold" style="width: 15%;">Ruolo</th>
                                        <th class="py-3 pe-4 text-center text-unibs fw-bold" style="width: 25%;">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($insegnamentiCorsi as $insC)
                                        <tr>
                                            <!-- Nome Insegnamento -->
                                            <td class="py-3 ps-4 fw-bold text-dark">
                                                {{ $insC->insegnamento->nome }}
                                            </td>

                                            <!-- Corso di Studi -->
                                            <td class="py-3 text-dark">
                                                <div class="fw-medium">{{ $insC->corso->nome }}</div>
                                                <div class="small text-muted fst-italic mt-1">
                                                    <i class="bi bi-bar-chart-steps me-1 text-unibs"></i>Anno di corso: {{ $insC->anno_di_corso }}
                                                </div>
                                            </td>

                                            <!-- Ruolo Docente -->
                                            <td class="py-3 text-dark">
                                                <span class="fw-semibold">
                                                    {{ $insC->ruolo_docente ?? 'Docente' }}
                                                </span>
                                            </td>

                                            <!-- Pulsanti Azioni Circolari con Etichetta Sotto -->
                                            <td class="py-3 pe-4 text-center text-nowrap">
                                                <div class="d-inline-flex align-items-center justify-content-center gap-4">
                                                    
                                                    <!-- Pulsante Circular VISUALIZZA -->
                                                    <a href="{{ route('docente.dettaglioInsegnamento', $insC->ins_corso_id) }}" 
                                                       class="text-decoration-none d-flex flex-column align-items-center group"
                                                       title="Visualizza Dettagli Insegnamento">
                                                        <div class="bg-white text-unibs border border-2 border-unibs rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                                             style="width: 38px; height: 38px;">
                                                            <i class="bi bi-eye-fill fs-6"></i>
                                                        </div>
                                                        <span class="fw-bold text-unibs mt-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">VISUALIZZA</span>
                                                    </a>

                                                    <!-- Pulsante Circular APPELLI -->
                                                    <a href="{{ route('docente.appelliInsegnamento', $insC->ins_corso_id) }}" 
                                                       class="text-decoration-none d-flex flex-column align-items-center group"
                                                       title="Gestisci Appelli per questo Insegnamento">
                                                        <div class="bg-white text-success border border-2 border-success rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                                             style="width: 38px; height: 38px;">
                                                            <i class="bi bi-calendar-plus-fill fs-6"></i>
                                                        </div>
                                                        <span class="fw-bold text-success mt-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">APPELLI</span>
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted fw-medium">
                                                <i class="bi bi-info-circle me-1 text-secondary"></i> Nessun insegnamento associato al tuo profilo.
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

    </body>
</html>