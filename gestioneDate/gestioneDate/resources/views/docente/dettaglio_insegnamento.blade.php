<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dettagli Insegnamento</title>

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

        <!-- Navbar Istituzionale -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="{{ asset('images/unibsLogo.png') }}" alt="Unibs" style="max-height: 70px;">
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               href="{{ route('docente.dashboard') }}" style="width: 115px;">
                                <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" style="width: 115px;">
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
                
                <!-- Header Intestazione -->
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>Dettagli Insegnamento
                        </h1>
                        <p class="text-muted mb-0 fw-medium">
                            Scheda informativa completa e corpo docente associato
                        </p>
                    </div>

                    <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-unibs fw-bold rounded-3 shadow-sm px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Dashboard
                    </a>
                </div>

                <!-- Banner Principale Insegnamento -->
                <div class="card shadow-sm border-0 border-start border-5 border-unibs rounded-3 mb-4 overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <span class="badge bg-unibs text-white px-3 py-1.5 mb-2 rounded-pill fw-semibold fs-7">
                                    <i class="bi bi-mortarboard-fill me-1"></i> {{ $insegnamentoCorso->corso->tipo_corso }}
                                </span>
                                <h2 class="h3 fw-bold text-dark mb-1">
                                    {{ $insegnamentoCorso->insegnamento->nome }}
                                </h2>
                                <div class="text-muted fs-6 fw-medium">
                                    <i class="bi bi-building me-1 text-unibs"></i> Corso di Laurea in {{ $insegnamentoCorso->corso->nome }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Griglia Metriche Informative -->
                <div class="row g-3 mb-4">
                    
                    <!-- Anno di Corso -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-metric-card p-3 h-100 d-flex align-items-center">
                            <div class="rounded-circle bg-light p-3 me-3 text-unibs d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-calendar3 fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small fw-bold text-uppercase">Anno di Corso</span>
                                <span class="fs-5 fw-bold text-dark">{{ $insegnamentoCorso->anno_di_corso }}° Anno</span>
                            </div>
                        </div>
                    </div>

                    <!-- Crediti CFU -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-metric-card p-3 h-100 d-flex align-items-center">
                            <div class="rounded-circle bg-light p-3 me-3 text-unibs d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-award fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small fw-bold text-uppercase">Crediti Formativi</span>
                                <span class="fs-5 fw-bold text-dark">{{ $insegnamentoCorso->cfu }} CFU</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ore Didattiche -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-metric-card p-3 h-100 d-flex align-items-center">
                            <div class="rounded-circle bg-light p-3 me-3 text-unibs d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small fw-bold text-uppercase">Ore Didattiche</span>
                                <span class="fs-5 fw-bold text-dark">{{ $insegnamentoCorso->durata }} Ore</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ruolo Personale Docente -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-metric-card p-3 h-100 d-flex align-items-center">
                            <div class="rounded-circle bg-light p-3 me-3 text-unibs d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-person-badge fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small fw-bold text-uppercase">Tuo Ruolo</span>
                                <span class="fs-5 fw-bold text-dark">{{ $docenteInsegnamento->ruolo ?? 'Docente' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Card Tabella Roster Docenti -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                    <div class="card-header bg-unibs text-white py-3 px-4 border-0 d-flex align-items-center">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                            <i class="bi bi-people-fill me-2 fs-5"></i> Copertura Didattica Insegnamento
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="py-3 px-4 text-unibs fw-bold">Docente Titolare / Incaricato</th>
                                        <th class="py-3 px-4 text-center text-unibs fw-bold" style="width: 200px;">Ruolo Assegnato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Docente Primario -->
                                    @php
                                        $isPrimarioMe = $docentePrimario && ($docentePrimario->utente_id == Auth::user()->getKey());
                                    @endphp
                                    <tr class="{{ $isPrimarioMe ? 'tr-docente-attivo' : '' }}">
                                        <td class="py-3 px-4 fw-semibold text-dark">
                                            @if($docentePrimario)
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-fill text-unibs fs-5 me-2"></i>
                                                    <span>Prof. {{ $docentePrimario->nome }} {{ $docentePrimario->cognome }}</span>
                                                    @if($isPrimarioMe)
                                                        <i class="bi bi-star-fill text-unibs ms-2" title="Tuo profilo docente"></i>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">
                                                    <i class="bi bi-exclamation-circle me-1"></i> Non assegnato
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge border border-unibs text-unibs bg-white rounded-pill px-3 py-2 fw-bold d-inline-block shadow-sm" style="width: 130px;">
                                                Primario
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Docenti Secondari -->
                                    @foreach($docentiSecondari as $sec)
                                        @php
                                            $isSecMe = ($sec->utente_id == Auth::user()->getKey());
                                        @endphp
                                        <tr class="{{ $isSecMe ? 'tr-docente-attivo' : '' }}">
                                            <td class="py-3 px-4 fw-semibold text-dark">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-fill text-secondary fs-5 me-2"></i>
                                                    <span>Prof. {{ $sec->nome }} {{ $sec->cognome }}</span>
                                                    @if($isSecMe)
                                                        <i class="bi bi-star-fill text-unibs ms-2" title="Tuo profilo docente"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge border border-secondary text-secondary bg-white rounded-pill px-3 py-2 fw-bold d-inline-block shadow-sm" style="width: 130px;">
                                                    Secondario
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bottone Inferiore di Navigazione A Tutta Larghezza -->
                <div class="row mt-4">
                    <div class="col-12">
                        <a href="{{ route('docente.dashboard') }}" 
                           class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-arrow-left me-2 fs-6"></i>Torna alla Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </body>
</html>