<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendario Generale Appelli - Amministratore</title>

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

        <script src="{{ asset('js/confirmDelete.js') }}"></script>

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

        <!-- Contenuto Principale -->
        <div class="container py-3">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Intestazione -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-calendar-check-fill me-2"></i>Calendario Generale Appelli
                    </h1>
                    <p class="text-muted small m-0 mt-1">Supervisione e gestione globale di tutti gli appelli d'esame</p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase">
                    <i class="bi bi-arrow-left me-2"></i>TORNA ALLA DASHBOARD
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- SCHEDA FILTRI FACOLTATIVI -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-3">
                    <form action="{{ route('admin.calendario') }}" method="GET" class="row g-3 align-items-end">
                        
                        <!-- 1. Filtro Corso (Facoltativo) -->
                        <div class="col-12 col-md-3">
                            <label for="corso_id" class="form-label fw-bold text-dark small mb-1">Corso di Laurea:</label>
                            <select name="corso_id" id="corso_id" class="form-select form-select-sm shadow-sm">
                                <option value="">-- Tutti i Corsi --</option>
                                @foreach($corsi as $corso)
                                    <option value="{{ $corso->corso_id }}" 
                                            data-durata="{{ $corso->durata_anni }}"
                                            {{ ($corso_id == $corso->corso_id) ? 'selected' : '' }}>
                                        {{ $corso->nome }} - {{ $corso->tipo_corso }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Filtro Sessione (dipende dal corso scelto) -->
                        <div class="col-12 col-md-3">
                            <label for="sessione_id" class="form-label fw-bold text-dark small mb-1">Sessione:</label>
                            <select name="sessione_id" id="sessione_id" class="form-select form-select-sm shadow-sm"
                                    data-testo-vuoto="-- Seleziona prima un Corso --"
                                    data-testo-attivo="-- Tutte le Sessioni del corso --"
                                    {{ $corso_id ? '' : 'disabled' }}>
                                <option value="">-- {{ $corso_id ? 'Tutte le Sessioni del corso' : 'Seleziona prima un Corso' }} --</option>
                                @foreach($sessioni as $sessione)
                                    <option value="{{ $sessione->sessione_id }}" 
                                            data-corso="{{ $sessione->corso_id }}"
                                            {{ ($sessione_id == $sessione->sessione_id) ? 'selected' : '' }}>
                                        {{ $sessione->nome }} ({{ $sessione->anno }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Filtro Anno di Corso (dipende dal corso scelto) -->
                        <div class="col-12 col-md-3">
                            <label for="anno_di_corso" class="form-label fw-bold text-dark small mb-1">Anno di Corso:</label>
                            <select name="anno_di_corso" 
                                    id="anno_di_corso" 
                                    class="form-select form-select-sm shadow-sm"
                                    data-initial-value="{{ $anno_di_corso }}"
                                    {{ $corso_id ? '' : 'disabled' }}>
                                <option value="">-- {{ $corso_id ? 'Tutti gli anni' : 'Seleziona prima un Corso' }} --</option>
                            </select>
                        </div>

                        <!-- 4. Pulsanti Filtra e Reset -->
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-unibs btn-sm text-white fw-bold w-100 shadow-sm rounded-2">
                                <i class="bi bi-funnel-fill me-1"></i> Filtra
                            </button>
                            @if(request()->hasAny(['sessione_id', 'corso_id', 'anno_di_corso']))
                                <a href="{{ route('admin.calendario') }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm rounded-2">
                                    <i class="bi bi-x-circle me-1"></i> Reset
                                </a>
                            @endif
                        </div>

                    </form>
                </div>
            </div>

            <!-- VISUALIZZAZIONE GERARCHICA APPELLI (SESSIONE -> CORSO -> ANNO DEGLI APPELLI) -->
            @if($appelliGrouped->isNotEmpty())
                @foreach($appelliGrouped as $sessionId => $corsiGroup)
                    @php
                        // Recupero dati della sessione
                        $firstAppello = $corsiGroup->first()->first()->first();
                        $sessione = $firstAppello->sessione;
                        $isPassata = $sessione ? \Carbon\Carbon::parse($sessione->data_fine)->lt(\Carbon\Carbon::today()) : false;
                    @endphp

                    <!-- Blocco Sessione d'Esame -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-unibs text-white py-3 px-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                            <h5 class="m-0 fw-bold fs-6">
                                <i class="bi bi-calendar-range-fill me-2"></i>Sessione: {{ $sessione->nome }} ({{ $sessione->anno }}){{ $sessione->corso ? ' - ' . $sessione->corso->nome . ' (' . $sessione->corso->tipo_corso . ')' : '' }}
                            </h5>
                            <span class="badge bg-white text-unibs fw-bold px-3 py-1.5 rounded-pill align-self-start align-self-md-auto">
                                Periodo: {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="card-body p-0">
                            @foreach($corsiGroup as $corsoNome => $anniGroup)
                                <!-- Sotto-intestazione Corso di Laurea -->
                                <div class="bg-light px-4 py-2 border-bottom fw-bold text-dark d-flex align-items-center">
                                    <i class="bi bi-journal-bookmark-fill me-2 text-unibs"></i>Corso: {{ $corsoNome }}
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light border-bottom text-muted small">
                                            <tr>
                                                <th class="py-2 px-4 text-dark fw-bold">Insegnamento</th>
                                                <th class="py-2 px-4 text-center text-dark fw-bold">Data e Orario</th>
                                                <th class="py-2 px-4 text-center text-dark fw-bold">Sede</th>
                                                <th class="py-2 px-4 text-center text-dark fw-bold">Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($anniGroup as $annoNumero => $appelli)
                                                <!-- Intestazione Anno di Corso -->
                                                <tr class="table-secondary border-top border-bottom">
                                                    <td colspan="4" class="py-1 px-4 fw-bold text-dark small">
                                                        <i class="bi bi-mortarboard-fill me-2 text-unibs"></i>
                                                        @if(is_numeric($annoNumero))
                                                            {{ $annoNumero }}° Anno
                                                        @else
                                                            {{ $annoNumero }}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Righe Singoli Appelli -->
                                                @foreach($appelli as $appello)
                                                    <tr>
                                                        <!-- Insegnamento -->
                                                        <td class="py-3 px-4 fw-semibold text-dark">
                                                            {{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }}
                                                        </td>

                                                        <!-- Data e Orario -->
                                                        <td class="py-3 px-4 text-center text-nowrap">
                                                            <span class="badge border border-unibs text-unibs bg-transparent px-3 py-2 rounded-3 fw-bold d-inline-block text-start">
                                                                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                                <br>
                                                                <i class="bi bi-clock me-1"></i>{{ substr($appello->ora_inizio, 0, 5) }} - {{ substr($appello->ora_fine, 0, 5) }}
                                                            </span>
                                                        </td>

                                                        <!-- Sede e Aula -->
                                                        <td class="py-3 px-4 text-center text-nowrap">
                                                            @if($appello->aula)
                                                                <span class="badge border border-unibs text-unibs bg-transparent px-3 py-2 rounded-3 fw-bold d-inline-block text-start">
                                                                    <i class="bi bi-building me-1"></i>{{ $appello->aula->edificio->nome ?? 'N/D' }}
                                                                    <br>
                                                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $appello->aula->nome }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted small">N/D</span>
                                                            @endif
                                                        </td>

                                                        <!-- Azioni (Disabilitate se la sessione è passata) -->
                                                        <td class="py-3 px-4 text-center text-nowrap">
                                                            <div class="d-flex justify-content-center gap-2">
                                                                @if($isPassata)
                                                                    <button type="button" class="btn bg-white border border-secondary text-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center opacity-50" style="width: 36px; height: 36px;" disabled title="Non è possibile modificare appelli di sessioni passate">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                    </button>
                                                                    <button type="button" class="btn bg-white border border-secondary text-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center opacity-50" style="width: 36px; height: 36px;" disabled title="Non è possibile eliminare appelli di sessioni passate">
                                                                        <i class="bi bi-trash-fill"></i>
                                                                    </button>
                                                                @else
                                                                    <a href="{{ route('admin.appelli.edit', $appello->getKey()) }}" class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Modifica Appello">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                    </a>
                                                                    <form action="{{ route('admin.appelli.destroy', $appello->getKey()) }}" 
                                                                        method="POST" 
                                                                        data-confirm="Sei sicuro di voler eliminare questo appello d'esame?" 
                                                                        class="d-inline m-0">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                                                style="width: 36px; height: 36px;" 
                                                                                title="Elimina Appello">
                                                                            <i class="bi bi-trash-fill"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body text-center text-muted py-5 fs-6">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                        Nessun appello trovato con i filtri selezionati.
                    </div>
                </div>
            @endif

        </div>

        <!-- Collegamento allo script dei filtri del calendario -->
        <script src="{{ asset('js/filtriCalendario.js') }}"></script>
        <script src="{{ asset('js/sessioniPerCorso.js') }}"></script>

    </body>
</html>