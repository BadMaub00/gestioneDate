<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Associazioni Docente-Insegnamento - Amministratore</title>

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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-people-fill me-2"></i>Associazioni Docenti - Insegnamenti
                    </h1>
                    <p class="text-muted small m-0 mt-1">Elenco e gestione dei docenti assegnati agli insegnamenti dei vari corsi</p>
                </div>

                <a href="{{ route('admin.personale.associa') }}" class="btn btn-unibs text-white fw-bold rounded-pill shadow-sm px-4 py-2">
                    <i class="bi bi-person-plus-fill me-2"></i>Aggiungi Associazione
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-unibs text-white">
                            <tr>
                                <th class="py-3 px-4">Docente</th>
                                <th class="py-3 px-4">Insegnamento</th>
                                <th class="py-3 px-4">Corso di Laurea</th>
                                <th class="py-3 px-4 text-center">Ruolo</th>
                                <th class="py-3 px-4 text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($associazioni as $assoc)
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-dark">
                                        {{ $assoc->docente->cognome ?? '' }} {{ $assoc->docente->nome ?? 'N/D' }}
                                    </td>
                                    <td class="py-3 px-4 fw-semibold">
                                        {{ $assoc->insegnamentiCorso->insegnamento->nome ?? 'N/D' }}
                                    </td>
                                    <td class="py-3 px-4 text-muted">
                                        {{ $assoc->insegnamentiCorso->corso->nome ?? 'N/D' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge {{ $assoc->ruolo === 'Primario' ? 'bg-primary' : 'bg-secondary' }} rounded-pill px-3 py-1.5">
                                            {{ $assoc->ruolo }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('admin.personale.destroyAssocia', $assoc->getKey()) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Sei sicuro di voler rimuovere questa associazione?');" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Rimuovi Associazione" style="width: 32px; height: 32px; padding: 3px 0;">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                        Nessuna associazione presente.
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