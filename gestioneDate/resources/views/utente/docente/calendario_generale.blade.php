<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendario Generale Appelli</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <!-- Script Bootstrap & Popper -->
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>

        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                
                <!-- Logo Università -->
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="{{ asset('images/unibsLogo.png') }}" alt="Unibs" style="max-height: 70px;">
                </a>

                <!-- Toggler Mobile -->
                <button class="navbar-toggler border-0 shadow-none" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" 
                        aria-expanded="false" 
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu di Navigazione -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <!-- Pulsante Home -->
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               aria-current="page" 
                               href="{{ route('docente.dashboard') }}"
                               style="width: 115px;">
                                <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                                <span>Home</span>
                            </a>
                        </li>

                        <!-- Pulsante Logout -->
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-danger btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start"
                                        style="width: 115px;">
                                    <i class="bi bi-box-arrow-right text-center me-2" style="width: 18px;"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


        <!-- Contenuto Principale -->
        <div class="py-3">
            <div class="container-fluid px-md-4 px-lg-5">
                
                <!-- Header Intestazione Calendario -->
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

                <!-- Legenda Colori -->
                <div class="mb-3 d-flex align-items-center gap-3 fs-7">
                    <span class="fw-bold text-dark">Legenda:</span>
                    <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 rounded-2 fw-semibold">
                        <i class="bi bi-star-fill text-warning me-1"></i> Tuo insegnamento (Primario o Secondario)
                    </span>
                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2 fw-normal">
                        Altri insegnamenti
                    </span>
                </div>

                <!-- LAYOUT REATTIVO AFFIANCATO -->
                <div class="row g-4 align-items-start">
                    
                    <!-- 1. VISTA CALENDARIO A GRIGLIA (COLONNA DI SINISTRA) -->
                    <div class="col-12 col-xl-7 col-xxl-8 order-2 order-xl-1">
                        
                        <!-- Card Calendario con Header Navigazione Settimanale -->
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

                    <!-- 2. TABELLA RIASSUNTIVA (COLONNA DI DESTRA) -->
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
                                                <th class="ps-3 py-3 text-unibs" style="width: 28%;">Data</th>
                                                <th class="py-3 text-unibs" style="width: 25%;">Orario</th>
                                                <th class="py-3 text-unibs" style="width: 47%;">Insegnamento & Docente</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($appelliAnnoCorso as $appello)
                                                <tr class="{{ $appello->is_mio_appello ? 'table-warning' : '' }}">
                                                    <!-- Data -->
                                                    <td class="ps-3 py-2.5 fw-bold text-dark fs-7">
                                                        <i class="bi bi-calendar-event text-unibs me-1"></i>
                                                        {{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                    </td>

                                                    <!-- Orario -->
                                                    <td class="py-2.5 text-dark fw-medium fs-7">
                                                        <i class="bi bi-clock me-1 text-muted"></i>
                                                        {{ \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($appello->ora_fine)->format('H:i') }}
                                                    </td>

                                                    <!-- Insegnamento e Docente -->
                                                    <td class="py-2.5 pe-3 text-dark">
                                                        <div class="fw-semibold text-dark d-flex align-items-center justify-content-between">
                                                            <span>{{ $appello->nome_insegnamento }}</span>
                                                            @if($appello->is_mio_appello)
                                                                <i class="bi bi-star-fill text-warning ms-1" title="Tuo insegnamento"></i>
                                                            @endif
                                                        </div>
                                                        <div class="small text-muted fst-italic">
                                                            @if ($appello->docente_nome && $appello->docente_cognome)
                                                                <i class="bi bi-person-fill me-1 text-unibs"></i>Prof. {{ $appello->docente_nome }} {{ $appello->docente_cognome }}
                                                            @else
                                                                Non assegnato
                                                            @endif
                                                        </div>
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

            </div>
        </div>

        <!-- Script JS Calendario -->
        <!-- Sostituisci la riga dello script in fondo con questa: -->
<script src="{{ asset('js/calendarioGenerale.js') }}?v={{ time() }}"></script>
    </body>
</html>