<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Personale - Amministratore</title>

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

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               href="{{ route('admin.dashboard') }}" style="width: 115px;">
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

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Modifica Utente</h1>
                        <a href="{{ route('admin.personale.index') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="m-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.personale.update', $utente->utente_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-bold text-dark small mb-1">
                                        Username <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="username" 
                                           id="username" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('username', $utente->username) }}" 
                                           required>
                                </div>

                                <!-- Nome e Cognome -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                            Nome <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               name="nome" 
                                               id="nome" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('nome', $utente->nome) }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cognome" class="form-label fw-bold text-dark small mb-1">
                                            Cognome <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               name="cognome" 
                                               id="cognome" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('cognome', $utente->cognome) }}" 
                                               required>
                                    </div>
                                </div>

                                <!-- Data di Nascita e Ruolo -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_nascita" class="form-label fw-bold text-dark small mb-1">
                                            Data di Nascita <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               name="data_nascita" 
                                               id="data_nascita" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('data_nascita', $utente->data_nascita) }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ruolo" class="form-label fw-bold text-dark small mb-1">
                                            Ruolo <span class="text-danger">*</span>
                                        </label>
                                        <select name="ruolo" id="ruolo" class="form-select shadow-sm" required>
                                            <option value="Docente" {{ old('ruolo', $utente->ruolo) == 'Docente' ? 'selected' : '' }}>Docente</option>
                                            <option value="Amministratore" {{ old('ruolo', $utente->ruolo) == 'Amministratore' ? 'selected' : '' }}>Amministratore</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Modifica Password (Opzionale) -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold text-dark small mb-1">
                                        Nuova Password <span class="text-muted fw-normal">(opzionale)</span>
                                    </label>
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="form-control shadow-sm" 
                                           placeholder="Lascia vuoto per non cambiarla">
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Aggiorna Utente
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>