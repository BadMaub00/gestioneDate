<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Associa Docente a Insegnamento</title>

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
                <div class="col-12 col-md-8">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-person-plus-fill me-2"></i>Associa Docente a Insegnamento
                        </h1>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                        </a>
                    </div>
                    <hr class="border-unibs border-2 opacity-100 mb-4">

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <form action="{{ route('admin.personale.storeAssocia') }}" method="POST">
                                @csrf

                                <!-- Seleziona Insegnamento / Corso -->
                                <div class="mb-3">
                                    <label for="ins_corso_id" class="form-label fw-bold">Insegnamento e Corso <span class="text-danger">*</span></label>
                                    <select name="ins_corso_id" id="ins_corso_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona un insegnamento --</option>
                                        @foreach($insegnamentiCorso as $ic)
                                            <option value="{{ $ic->ins_corso_id }}">
                                                {{ $ic->insegnamento->nome ?? 'Insegnamento N/D' }} 
                                                ({{ $ic->corso->nome ?? 'Corso N/D' }} - {{ $ic->anno_di_corso }}° Anno)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Seleziona Docente -->
                                <div class="mb-3">
                                    <label for="utente_id" class="form-label fw-bold">Docente <span class="text-danger">*</span></label>
                                    <select name="utente_id" id="utente_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona un docente --</option>
                                        @foreach($docenti as $d)
                                            <option value="{{ $d->utente_id }}">
                                                {{ $d->cognome }} {{ $d->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Seleziona Ruolo -->
                                <div class="mb-4">
                                    <label for="ruolo" class="form-label fw-bold">Ruolo <span class="text-danger">*</span></label>
                                    <select name="ruolo" id="ruolo" class="form-select" required>
                                        <option value="Primario">Primario</option>
                                        <option value="Secondario">Secondario</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Salva Associazione
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