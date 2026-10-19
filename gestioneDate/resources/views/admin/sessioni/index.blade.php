<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestione Sessioni d'Esame</title>

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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Titolo e Tasto Torna alla Dashboard -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-2">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-calendar-range me-2"></i>Gestione Sessioni d'Esame
                    </h1>
                    <p class="text-muted small m-0 mt-1">Elenco e gestione delle sessioni d'esame registrate nel sistema</p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold text-uppercase rounded-pill px-4 py-2 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Torna alla Dashboard
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Pulsante Principale Aggiungi Sessione (Full Width) -->
            <div class="mb-4">
                <a href="{{ route('sessioni.create') }}" class="btn btn-unibs text-white fw-bold text-uppercase w-100 py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center fs-5">
                    <i class="bi bi-plus-lg me-2"></i>Aggiungi Sessione
                </a>
            </div>

            <!-- Tabella Sessioni -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Nome</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Anno</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Periodo</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">N° Appelli</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessioni as $s)
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-dark">{{ $s->nome }}</td>
                                    <td class="py-3 px-4 text-center fw-bold text-dark">{{ $s->anno }}</td>
                                    <td class="py-3 px-4 text-center text-muted">
                                        {{ \Carbon\Carbon::parse($s->data_inizio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($s->data_fine)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-center fw-bold text-dark">
                                        {{ $s->numero_appelli }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('sessioni.edit', $s->sessione_id) }}" 
                                               class="btn btn-sm btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" 
                                               title="Modifica Sessione" 
                                               style="width: 36px; height: 36px;">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <form action="{{ route('sessioni.destroy', $s->sessione_id) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Sei sicuro di voler eliminare questa sessione?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" 
                                                        title="Elimina Sessione" 
                                                        style="width: 36px; height: 36px;">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                                        Nessuna sessione d'esame presente nel sistema.
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