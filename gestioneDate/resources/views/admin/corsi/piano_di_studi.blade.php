<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Piano di Studi - {{ $corso->nome }}</title>

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
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-journal-bookmark-fill me-2"></i>Piano di Studi: {{ $corso->nome }}
                    </h1>
                    <span class="badge bg-unibs text-white mt-1">{{ $corso->tipo_corso }}</span>
                </div>
                <a href="{{ route('admin.corsi.index') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Torna ai Corsi
                </a>
            </div>
            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Anni del corso (3, 2 o 5 anni) -->
            @for ($anno = 1; $anno <= $durataAnni; $anno++)
                <div class="card shadow-sm mb-4 border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-unibs text-white fw-bold py-2">
                        <i class="bi bi-calendar3 me-2"></i>{{ $anno }}° Anno di Corso
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($insegnamentiPerAnno->get($anno, []) as $ic)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 {{ $loop->even ? 'bg-light' : '' }}">
                                    <div>
                                        <strong class="fs-6 text-dark">{{ $ic->insegnamento->nome ?? 'Insegnamento N/D' }}</strong>
                                        <span class="text-muted d-block small">Codice: {{ $ic->insegnamento_id }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 me-1">{{ $ic->cfu }} CFU</span>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ $ic->durata }} ore</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted fst-italic py-3 px-4">
                                    Nessun insegnamento inserito per il {{ $anno }}° anno.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endfor
        </div>

    </body>
</html>