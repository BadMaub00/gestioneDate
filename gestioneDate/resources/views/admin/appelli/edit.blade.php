<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Appello - Amministratore</title>

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

        <!-- Navbar Istituzionale -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="{{ asset('images/unibsLogo.png') }}" alt="Unibs" style="max-height: 70px;">
                </a>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               href="{{ route('admin.dashboard') }}" style="width: 115px;">
                                <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                                <span>Home</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenuto Principale -->
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-7">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-pencil-square me-2"></i>Modifica Appello d'Esame
                        </h1>
                        <a href="{{ route('admin.calendario', $appello->sessione_id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i>Annulla
                        </a>
                    </div>
                    <hr class="border-unibs border-2 opacity-100 mb-4">

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <!-- Il modulo invia i dati alla rotta update mediante metodo PUT -->
                            <form action="{{ route('admin.appelli.update', $appello->getKey()) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Insegnamento e Corso (Solo lettura) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Insegnamento / Corso</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="{{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }} ({{ $appello->insegnamentoCorso->corso->nome ?? 'N/D' }})" disabled>
                                </div>

                                <!-- Data dell'Appello -->
                                <div class="mb-3">
                                    <label for="data" class="form-label fw-bold">Data Esame <span class="text-danger">*</span></label>
                                    <input type="date" name="data" id="data" class="form-control" value="{{ $appello->data }}" required>
                                </div>

                                <!-- Orari Inizio e Fine -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <label for="ora_inizio" class="form-label fw-bold">Ora Inizio <span class="text-danger">*</span></label>
                                        <input type="time" name="ora_inizio" id="ora_inizio" class="form-control" value="{{ $appello->ora_inizio }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="ora_fine" class="form-label fw-bold">Ora Fine <span class="text-danger">*</span></label>
                                        <input type="time" name="ora_fine" id="ora_fine" class="form-control" value="{{ $appello->ora_fine }}" required>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Salva Modifiche
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>