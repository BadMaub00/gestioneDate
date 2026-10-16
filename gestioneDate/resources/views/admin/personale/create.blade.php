<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuovo Personale Universitario</title>

        <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/docente.css') }}">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="{{ asset('images/unibsLogo.png') }}" alt="Unibs" style="max-height: 70px;">
                </a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-house-door-fill me-2"></i>Home
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Aggiungi Personale</h1>
                        <a href="{{ route('admin.personale.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Annulla</a>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.personale.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" required value="{{ old('nome') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="cognome" class="form-label fw-bold">Cognome <span class="text-danger">*</span></label>
                                    <input type="text" name="cognome" id="cognome" class="form-control" required value="{{ old('cognome') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" class="form-control" required value="{{ old('username') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control" required minlength="6">
                                </div>

                                <div class="mb-3">
                                    <label for="data_nascita" class="form-label fw-bold">Data di Nascita <span class="text-danger">*</span></label>
                                    <input type="date" name="data_nascita" id="data_nascita" class="form-control" required value="{{ old('data_nascita') }}">
                                </div>

                                <div class="mb-4">
                                    <label for="ruolo" class="form-label fw-bold">Ruolo <span class="text-danger">*</span></label>
                                    <select name="ruolo" id="ruolo" class="form-select" required>
                                        <option value="Docente">Docente</option>
                                        <option value="Amministratore">Amministratore</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-person-plus-fill me-2"></i>Crea Utente
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>
