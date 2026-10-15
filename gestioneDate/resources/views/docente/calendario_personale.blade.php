<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendario Personale - Docente</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/docente.css') }}">
    </head>
    <body class="bg-light min-vh-100">

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

        <!-- Estrazione e Formattazione Dati Appelli per JS -->
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
                        'ruolo_docente' => $insC->ruolo_docente ?? 'Secondario',
                    ];
                }
            }
        @endphp

        <!-- Contenuto Principale -->
        <div class="py-3">
            <div class="container-fluid px-md-4 px-lg-5">
                
                <!-- Header Intestazione Docente -->
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                            <i class="bi bi-calendar-check-fill me-2"></i>Calendario Personale Appelli
                        </h1>
                        <p class="text-muted mb-0 fw-medium">
                            <strong>Docente:</strong> Prof. {{ Auth::user()->nome }} {{ Auth::user()->cognome }} | 
                            <strong>Sessione:</strong> {{ $sessione->nome }} ({{ $sessione->anno }})
                        </p>
                    </div>

                    <a href="{{ route('docente.selezionaSessione') }}" class="btn btn-outline-unibs fw-bold rounded-3 shadow-sm px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Cambia Sessione
                    </a>
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
                                     data-appelli='@json($appelliArray)' 
                                     data-inizio="{{ $sessione->data_inizio }}" 
                                     data-fine="{{ $sessione->data_fine }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. TABELLA RIASSUNTIVA CON AZIONI (COLONNA DI DESTRA) -->
                    <div class="col-12 col-xl-5 col-xxl-4 order-1 order-xl-2">
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white px-4 border-0 d-flex justify-content-between align-items-center" style="min-height: 62px;">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-list-check me-2 fs-5"></i> I Miei Appelli
                                </h5>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 680px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light border-bottom sticky-top">
                                            <tr>
                                                <th class="ps-3 py-3 text-unibs" style="width: 32%;">Data & Orario</th>
                                                <th class="py-3 text-unibs" style="width: 38%;">Insegnamento</th>
                                                <th class="pe-3 py-3 text-unibs text-end" style="width: 30%;">Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hasAppelli = false; @endphp
                                            @foreach($insegnamentiCorsi as $insC)
                                                @foreach($insC->appelli as $appello)
                                                    @php $hasAppelli = true; @endphp
                                                    <tr>
                                                        <!-- Data e Orario -->
                                                        <td class="ps-3 py-2.5 text-dark fs-7">
                                                            <div class="fw-bold">
                                                                <i class="bi bi-calendar-event text-unibs me-1"></i>
                                                                {{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                            </div>
                                                            <div class="small text-muted fw-medium mt-0.5">
                                                                <i class="bi bi-clock me-1"></i>
                                                                {{ \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($appello->ora_fine)->format('H:i') }}
                                                            </div>
                                                        </td>

                                                        <!-- Insegnamento e Corso -->
                                                        <td class="py-2.5 text-dark">
                                                            <div class="fw-semibold text-dark">{{ $insC->insegnamento->nome }}</div>
                                                            <div class="small text-muted fst-italic">
                                                                <i class="bi bi-mortarboard me-1 text-unibs"></i>{{ $insC->corso->nome }}
                                                            </div>
                                                        </td>

                                                        <!-- Pulsanti Modifica / Elimina gestiti per Ruolo -->
                                                        <td class="text-end pe-3 py-2.5">
                                                            <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Azioni appello">
                                                                @if(($insC->ruolo_docente ?? 'Secondario') === 'Primario')
                                                                    <!-- MODIFICA (Giallo Warning) - Abilitato -->
                                                                    <a href="{{ route('appelli.edit', $appello->appello_id) }}" 
                                                                       class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center justify-content-center px-2 py-1" 
                                                                       style="width: 90px;"
                                                                       title="Modifica Appello">
                                                                        <i class="bi bi-pencil-square me-1"></i> Modifica
                                                                    </a>

                                                                    <!-- ELIMINA (Rosso Danger) - Abilitato -->
                                                                    <form action="{{ route('appelli.destroy', $appello->appello_id) }}" 
                                                                          method="POST" 
                                                                          class="d-inline m-0 p-0"
                                                                          onsubmit="return confirm('Sei sicuro di voler eliminare questo appello?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn btn-danger text-white fw-bold d-inline-flex align-items-center justify-content-center px-2 py-1" 
                                                                                style="width: 90px; border-top-left-radius: 0; border-bottom-left-radius: 0;" 
                                                                                title="Elimina Appello">
                                                                            <i class="bi bi-trash me-1"></i> Elimina
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <!-- MODIFICA (Giallo Warning) - Disabilitato con Sbarramento -->
                                                                    <button type="button" 
                                                                            class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center justify-content-center px-2 py-1 opacity-50" 
                                                                            disabled 
                                                                            style="width: 90px; cursor: not-allowed;"
                                                                            title="Azione riservata al docente primario dell'insegnamento">
                                                                        <i class="bi bi-slash-circle me-1"></i> Modifica
                                                                    </button>

                                                                    <!-- ELIMINA (Rosso Danger) - Disabilitato con Sbarramento -->
                                                                    <button type="button" 
                                                                            class="btn btn-danger text-white fw-bold d-inline-flex align-items-center justify-content-center px-2 py-1 opacity-50" 
                                                                            disabled 
                                                                            style="width: 90px; border-top-left-radius: 0; border-bottom-left-radius: 0; cursor: not-allowed;" 
                                                                            title="Azione riservata al docente primario dell'insegnamento">
                                                                        <i class="bi bi-slash-circle me-1"></i> Elimina
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                            @if(!$hasAppelli)
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted fw-bold">
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

                <!-- Bottoni di Navigazione Inferiori -->
                <div class="row g-2 mt-4">
                    <div class="col-12 col-md-6">
                        <a href="{{ route('docente.dashboard') }}" 
                           class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-arrow-left me-2 fs-6"></i>Torna alla Dashboard
                        </a>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('docente.selezionaSessione') }}" 
                           class="btn btn-unibs text-white fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-event me-2 fs-6"></i>Seleziona Un'Altra Sessione
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Script JS Calendario Personale -->
        <script src="{{ asset('js/calendarioPersonale.js') }}"></script>
    </body>
</html>