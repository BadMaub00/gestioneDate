<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Selezione Calendario Generale</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/docente.css') }}">
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
        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        
                        <!-- Card Selezione Filtri Calendario Generale -->
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2 fs-5"></i> Selezione Calendario Generale
                                </h5>
                            </div>

                            <div class="card-body p-4 bg-white">
                                @if($errors->any())
                                    <div class="alert alert-danger rounded-3 mb-3">
                                        <ul class="m-0 ps-3">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('docente.procedicalendario_generale') }}" method="POST">
                                    @csrf

                                    <!-- 1. SELECT CORSO -->
                                    <div class="mb-3">
                                        <label for="corso_id" class="form-label fw-bold text-dark mb-1">
                                            1. Corso di Studi:
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" 
                                                id="corso_id" 
                                                name="corso_id" 
                                                data-url-template="{{ route('docente.api.anniCorso', ['corso_id' => ':id']) }}" 
                                                required>
                                            <option value="" selected disabled>-- Seleziona Corso --</option>
                                            @forelse ($corsi as $corso)
                                                <option value="{{ $corso->corso_id }}">{{ $corso->nome }}</option>
                                            @empty
                                                <option value="" disabled>Nessun corso disponibile</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- 2. SELECT ANNO DI CORSO -->
                                    <div class="mb-3">
                                        <label for="annoCorso" class="form-label fw-bold text-dark mb-1">
                                            2. Anno di Corso:
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="annoCorso" name="annoCorso" disabled required>
                                            <option value="" selected disabled>-- Seleziona prima un Corso --</option>
                                        </select>
                                    </div>

                                    <!-- 3. SELECT SESSIONE -->
                                    <div class="mb-4">
                                        <label for="sessione_id" class="form-label fw-bold text-dark mb-1">
                                            3. Sessione d'Esame (del corso scelto):
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="sessione_id" name="sessione_id" required disabled
                                                data-testo-vuoto="-- Seleziona prima un Corso --"
                                                data-testo-attivo="-- Seleziona Sessione --">
                                            <option value="" selected disabled>-- Seleziona prima un Corso --</option>
                                            @foreach ($sessioni as $sessione)
                                                <option value="{{ $sessione->sessione_id }}" data-corso="{{ $sessione->corso_id }}">
                                                    {{ $sessione->nome }} ({{ $sessione->anno }}) - [dal {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4 pt-2 border-top">
                                        <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-secondary fw-bold px-3 py-2 rounded-3">
                                            <i class="bi bi-arrow-left me-1"></i> Annulla
                                        </a>
                                        <button type="submit" class="btn btn-unibs text-white fw-bold px-4 py-2 rounded-3 shadow-sm">
                                            Visualizza Calendario <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Script JS Esterno -->
        <script src="{{ asset('js/selezionaFiltriGenerale.js') }}"></script>
        <script src="{{ asset('js/sessioniPerCorso.js') }}"></script>
    </body>
</html>