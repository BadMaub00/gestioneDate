<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Personale Universitario - Università degli Studi di Brescia</title>

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
                <a class="navbar-brand py-0" href="{{ route('admin.dashboard') }}">
                    <img class="img-logo" src="{{ asset('images/unibsLogo.png') }}" alt="Unibs" style="max-height: 70px;">
                </a>

                <button class="navbar-toggler border-0 shadow-none" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" 
                        aria-expanded="false" 
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               href="{{ route('admin.dashboard') }}"
                               style="width: 115px;">
                                <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                                <span>Home</span>
                            </a>
                        </li>

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
        <div class="container py-3">
            
            <!-- Messaggi di Feedback -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Titolo, Sottotitolo e Pulsante Torna alla Dashboard -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-people-fill me-2"></i>Personale Universitario
                    </h1>
                    <p class="text-muted small mb-0 mt-1">Elenco e gestione degli utenti registrati nel sistema</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase">
                    <i class="bi bi-arrow-left me-2"></i>TORNA ALLA DASHBOARD
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Blocco Strutturale Aggiungi Personale -->
            <div class="mb-4">
                <a href="{{ route('admin.personale.create') }}" class="btn btn-unibs text-white fw-bold w-100 py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center text-uppercase fs-6">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i> AGGIUNGI PERSONALE
                </a>
            </div>

            <!-- Tabella Personale (Omologata) -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Nome</th>
                                <th class="py-3 px-4 text-dark fw-bold">Cognome</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Ruolo</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personale as $p)
                                <tr>
                                    <td class="py-3 px-4 fw-semibold text-dark">{{ $p->nome }}</td>
                                    <td class="py-3 px-4 fw-semibold text-dark">{{ $p->cognome }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge border border-unibs text-unibs bg-transparent rounded-pill py-2 fw-bold d-inline-block" style="width: 140px;">
                                            {{ $p->ruolo }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Pulsante Circolare Modifica -->
                                            <a href="#" class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Modifica Utente">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Pulsante Circolare Elimina -->
                                            <form action="#" method="POST" class="d-inline m-0" onsubmit="return confirm('Sei sicuro di voler eliminare questo utente?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Elimina Utente">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                        Nessun utente presente nel sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </body>
</html>