<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestione Sessioni d'Esame - Amministratore</title>

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

        <!-- Contenuto Principale -->
        <div class="py-3">
            <div class="container-fluid px-md-4 px-lg-5">

                <!-- Intestazione Pagina con Pulsante Torna alla Dashboard -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-calendar-range-fill me-2"></i>Gestione Sessioni d'Esame
                        </h1>
                        <p class="text-muted small m-0 mt-1 fw-medium">
                            Elenco e gestione delle sessioni d'esame registrate nel sistema
                        </p>
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase text-nowrap">
                        <i class="bi bi-arrow-left me-2"></i>TORNA ALLA DASHBOARD
                    </a>
                </div>

                <hr class="border-unibs border-2 opacity-100 mb-4">

                <!-- Alert Messaggi di Successo/Errore -->
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

                <!-- FILTRI OPZIONALI: CORSO E ANNO SESSIONE -->
                @php
                    $anniDisponibili = $sessioni->pluck('anno')->unique()->sort()->values();
                @endphp

                @if($anniDisponibili->count() > 0)
                    <div class="card shadow-sm border-0 rounded-3 mb-4 p-3 bg-white">
                        <div class="row align-items-center g-2">
                            <div class="col-12 col-md-auto fw-bold text-unibs d-flex align-items-center">
                                <i class="bi bi-funnel-fill me-2"></i>Filtra:
                            </div>
                            <div class="col-12 col-md-4">
                                <select id="filtroCorsoSessione" class="form-select form-select-sm shadow-sm border-unibs fw-semibold text-dark">
                                    <option value="tutti" selected>Tutti i corsi</option>
                                    @foreach($corsi as $corso)
                                        <option value="{{ $corso->corso_id }}">{{ $corso->nome }} - {{ $corso->tipo_corso }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <select id="filtroAnnoSessione" class="form-select form-select-sm shadow-sm border-unibs fw-semibold text-dark">
                                    <option value="tutti" selected>Tutti gli anni</option>
                                    @foreach($anniDisponibili as $anno)
                                        <option value="{{ $anno }}">Anno {{ $anno }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto text-muted small ms-md-auto">
                                <i class="bi bi-info-circle me-1"></i> I filtri si possono combinare
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Avviso in caso nessun risultato corrisponda al filtro -->
                <div id="noSessioniMatchFilter" class="card shadow-sm border-0 rounded-3 p-4 text-center text-muted d-none mb-4 bg-white">
                    <i class="bi bi-funnel fs-3 text-secondary mb-2"></i>
                    <h6 class="fw-bold m-0">Nessuna sessione trovata per i filtri selezionati.</h6>
                    <small>Imposta "Tutti i corsi" e "Tutti gli anni" per mostrare nuovamente tutte le sessioni d'esame.</small>
                </div>

                <!-- Bottone Aggiungi Sessione -->
                <div class="mb-4">
                    <a href="{{ route('admin.sessioni.create') }}" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center text-uppercase">
                        <i class="bi bi-plus-circle-fill me-2 fs-5"></i> AGGIUNGI SESSIONE
                    </a>
                </div>

                <!-- Tabella Sessioni d'Esame -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="ps-4 py-3 text-unibs" style="width: 20%;">Nome</th>
                                        <th class="py-3 text-unibs" style="width: 25%;">Corso di Studi</th>
                                        <th class="py-3 text-unibs text-center" style="width: 8%;">Anno</th>
                                        <th class="py-3 text-unibs text-center" style="width: 22%;">Periodo</th>
                                        <th class="py-3 text-unibs text-center" style="width: 10%;">N° Appelli</th>
                                        <th class="pe-4 py-3 text-unibs text-end" style="width: 15%;">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sessioni->sortBy([['data_inizio', 'asc'], ['corso_id', 'asc']]) as $sessione)
                                        @php
                                            // Verifica se la sessione si è conclusa prima di oggi
                                            $isPassata = \Carbon\Carbon::parse($sessione->data_fine)->lt(\Carbon\Carbon::today());
                                        @endphp
                                        <tr class="sessione-row" data-anno="{{ $sessione->anno }}" data-corso="{{ $sessione->corso_id }}">
                                            <!-- Nome -->
                                            <td class="ps-4 py-3 fw-bold text-dark">
                                                {{ $sessione->nome }}
                                            </td>

                                            <!-- Corso di Studi -->
                                            <td class="py-3 text-dark">
                                                @if($sessione->corso)
                                                    {{ $sessione->corso->nome }} <span class="text-muted small">- {{ $sessione->corso->tipo_corso }}</span>
                                                @else
                                                    <span class="text-muted">Non assegnato</span>
                                                @endif
                                            </td>

                                            <!-- Anno -->
                                            <td class="py-3 text-center fw-bold text-dark">
                                                {{ $sessione->anno }}
                                            </td>

                                            <!-- Periodo -->
                                            <td class="py-3 text-center text-muted fw-medium">
                                                {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}
                                            </td>

                                            <!-- N° Appelli -->
                                            <td class="py-3 text-center fw-bold text-dark">
                                                {{ $sessione->numero_appelli }}
                                            </td>

                                            <!-- Azioni -->
                                            <td class="pe-4 py-3 text-end text-nowrap">
                                                <div class="d-flex justify-content-end gap-1">
                                                    @if($isPassata)
                                                        <!-- Modifica Disabilitata per Sessioni Passate -->
                                                        <button type="button" 
                                                                class="btn btn-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50" 
                                                                disabled 
                                                                style="width: 32px; height: 32px;" 
                                                                title="Non è possibile modificare una sessione passata">
                                                            <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                                        </button>
                                                    @else
                                                        <!-- Modifica Attiva -->
                                                        <a href="{{ route('admin.sessioni.edit', $sessione->sessione_id) }}" 
                                                           class="btn btn-white text-unibs border border-unibs rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                           style="width: 32px; height: 32px;" 
                                                           title="Modifica Sessione">
                                                            <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                                        </a>
                                                    @endif

                                                    <!-- Elimina con Modal Personalizzato contenente Anno e Nome Sessione -->
                                                    <form action="{{ route('admin.sessioni.destroy', $sessione->sessione_id) }}" 
                                                          method="POST" 
                                                          class="d-inline m-0 p-0"
                                                          data-confirm="Sei sicuro di voler eliminare la sessione '{{ $sessione->nome }}' dell'anno {{ $sessione->anno }}{{ $sessione->corso ? ' per il corso ' . $sessione->corso->nome . ' (' . $sessione->corso->tipo_corso . ')' : '' }}?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-white text-danger border border-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                                style="width: 32px; height: 32px;" 
                                                                title="Elimina Sessione">
                                                            <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted fw-medium">
                                                <i class="bi bi-info-circle me-1 text-secondary"></i> Nessuna sessione d'esame registrata.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Script Filtraggio Dinamico per Corso e Anno Sessione -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filtroAnno = document.getElementById('filtroAnnoSessione');
                const filtroCorso = document.getElementById('filtroCorsoSessione');
                if (!filtroAnno || !filtroCorso) return;

                const sessionRows = document.querySelectorAll('.sessione-row');
                const noMatchAlert = document.getElementById('noSessioniMatchFilter');

                function applicaFiltri() {
                    const anno = filtroAnno.value;
                    const corso = filtroCorso.value;
                    let visibleCount = 0;

                    sessionRows.forEach(row => {
                        const okAnno = anno === 'tutti' || row.getAttribute('data-anno') === anno;
                        const okCorso = corso === 'tutti' || row.getAttribute('data-corso') === corso;

                        if (okAnno && okCorso) {
                            row.classList.remove('d-none');
                            visibleCount++;
                        } else {
                            row.classList.add('d-none');
                        }
                    });

                    noMatchAlert.classList.toggle('d-none', visibleCount !== 0 || sessionRows.length === 0);
                }

                filtroAnno.addEventListener('change', applicaFiltri);
                filtroCorso.addEventListener('change', applicaFiltri);
            });
        </script>

        <!-- Script Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>