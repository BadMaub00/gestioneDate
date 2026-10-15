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
    </head>
    <body class="bg-light min-vh-100">

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
                    <div class="col-12 col-xl-10">

                        <style>
                            .dash-grid { max-width: 720px; margin: 0 auto; }
                            .dash-tile {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                gap: .6rem;
                                min-height: 150px;
                                height: 100%;
                                padding: .75rem;
                                background: #f3f5fa;
                                border-radius: 14px;
                                box-shadow: 0 3px 8px rgba(0, 0, 0, .18);
                                color: #212529;
                                font-weight: 500;
                                text-align: center;
                                text-decoration: none;
                                transition: transform .15s ease, box-shadow .15s ease;
                            }
                            .dash-tile i { font-size: 3rem; line-height: 1; }
                            .dash-tile:hover { transform: translateY(-3px); box-shadow: 0 6px 14px rgba(0, 0, 0, .25); color: #212529; }
                        </style>

                        <!-- Titolo Intestazione Amministratore Dinamico -->
                        <div class="mb-4">
                            <h1 class="h3 fw-bold text-unibs mt-3 mb-2 d-flex align-items-center">
                                <i class="bi bi-person-badge me-2"></i>Amministratore: {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                            </h1>
                            <hr class="border-unibs border-2 opacity-100 my-0">
                        </div>

                        <!-- Riquadri (3 colonne, disposizione richiesta) -->
                        <!-- Riquadri (Centrati nello schermo) -->
                        <div class="dash-grid mx-auto mb-5">
                            <div class="row g-3 justify-content-center">

                                <!-- Riga 1: Corsi (Sx), Insegnamenti (Centro) -->
                                <div class="col-4">
                                    <a href="{{route('admin.corsi.index')}}" class="dash-tile">   
                                        <i class="bi bi-mortarboard-fill text-unibs"></i>
                                        <span>Corsi</span>
                                    </a>
                                </div>

                                <div class="col-4">
                                    <a href="{{ route('admin.personale.index') }}" class="dash-tile">
                                        <i class="bi bi-people-fill text-unibs"></i>
                                        <span>Personale universitario</span>
                                    </a>
                                </div>

                                <div class="col-4">
                                    <a href="{{ route('admin.personale.associa') }}" class="dash-tile">
                                        <i class="bi bi-people-fill text-unibs"></i>
                                        <span>Associa</span>
                                    </a>
                                </div>

                                <!-- Riga 3: Appelli (Sx), Sessioni (Centro) -->
                                <div class="w-100"></div> {{-- Forza l'andata a capo pulita --}}
                                <div class="col-4">
                                    <a href="{{ route('admin.calendario') }}" class="dash-tile">
                                        <i class="bi bi-calendar-check text-unibs"></i>
                                        <span>Appelli</span>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('sessioni.index') }}" class="dash-tile">
                                        <i class="bi bi-calendar-range text-unibs"></i>
                                        <span>Sessioni</span>
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