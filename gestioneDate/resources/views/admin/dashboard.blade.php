<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Amministratore</title>

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
                               href="{{ route('admin.dashboard') }}"
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
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">

                        <!-- Titolo Intestazione Amministratore Dinamico -->
                        <div class="mb-4">
                            <h1 class="h3 fw-bold text-unibs mt-3 mb-2 d-flex align-items-center">
                                <i class="bi bi-person-badge me-2"></i>Amministratore: {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                            </h1>
                            <hr class="border-unibs border-2 opacity-100 my-0">
                        </div>

                        <!-- Riquadri (Centrati nello schermo e più ampi) -->
                        <div class="mx-auto mb-5" style="max-width: 960px;">
                            <div class="row g-3 justify-content-center">

                                <!-- 1. Corsi -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.corsi.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">   
                                        <i class="bi bi-mortarboard-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Corsi</span>
                                    </a>
                                </div>

                                <!-- 2. Insegnamenti -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.insegnamenti.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-journal-bookmark-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Insegnamenti</span>
                                    </a>
                                </div>

                                <!-- 3. Personale Universitario -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.personale.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-people-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Personale universitario</span>
                                    </a>
                                </div>

                                <!-- 4. Associa -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.personale.associazioni_index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-people-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Incarichi Docenti</span>
                                    </a>
                                </div>

                                <!-- 5. Appelli -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.calendario') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-calendar-check text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Appelli</span>
                                    </a>
                                </div>

                                <!-- 6. Sessioni -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.sessioni.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-calendar-range text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Sessioni</span>
                                    </a>
                                </div>

                                <!-- 7. Sedi (Posizionato al centro della terza fila) -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.sedi.edifici') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-building text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Sedi</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </body>
</html>