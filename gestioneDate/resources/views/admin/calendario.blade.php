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
        <div class="container py-3">
            
            <!-- Alert messaggi di successo -->
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

            <!-- Intestazione e Pulsante Torna alla Dashboard -->
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

            <!-- Form Selezione Sessione e Corso -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-3">
                    <form action="{{ route('admin.calendario') }}" method="GET" class="row g-3 align-items-end">
                        
                        <!-- Selezione Sessione -->
                        <div class="col-12 col-md-5">
                            <label for="sessione_id" class="form-label fw-bold text-dark small mb-1">Sessione:</label>
                            <select name="sessione_id" id="sessione_id" class="form-select form-select-sm shadow-sm" required>
                                <option value="" disabled {{ empty($sessione_id) ? 'selected' : '' }}>-- Seleziona Sessione --</option>
                                @foreach($sessioni as $sessione)
                                    <option value="{{ $sessione->sessione_id }}" {{ ($sessione_id == $sessione->sessione_id) ? 'selected' : '' }}>
                                        {{ $sessione->nome }} ({{ $sessione->anno }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Selezione Corso -->
                        <div class="col-12 col-md-5">
                            <label for="corso_id" class="form-label fw-bold text-dark small mb-1">Corso di Laurea:</label>
                            <select name="corso_id" id="corso_id" class="form-select form-select-sm shadow-sm" required>
                                <option value="" disabled {{ empty($corso_id) ? 'selected' : '' }}>-- Seleziona Corso --</option>
                                @foreach($corsi as $corso)
                                    <option value="{{ $corso->corso_id }}" {{ ($corso_id == $corso->corso_id) ? 'selected' : '' }}>
                                        {{ $corso->nome }} - {{ $corso->tipo_corso }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pulsante Filtra -->
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-unibs btn-sm text-white fw-bold w-100 shadow-sm rounded-2">
                                <i class="bi bi-funnel-fill me-1"></i> Mostra
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Tabella degli Appelli Divisi per Anno di Corso -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Insegnamento</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Data e Orario</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Sede</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($appelliGrouped->isNotEmpty())
                                @foreach($appelliGrouped as $anno => $appelli)
                                    <!-- Intestazione Gruppo Anno di Corso -->
                                    <tr class="table-light">
                                        <td colspan="4" class="py-2 px-4 fw-bold text-unibs border-top border-bottom">
                                            <i class="bi bi-mortarboard-fill me-2"></i>
                                            @if(is_numeric($anno))
                                                {{ $anno }}° Anno
                                            @else
                                                {{ $anno }}
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Riga Singolo Appello -->
                                    @foreach($appelli as $appello)
                                        @php
                                            // Verifica se la sessione a cui appartiene l'appello è passata
                                            $sessioneAppello = $appello->sessione;
                                            $isPassata = $sessioneAppello ? \Carbon\Carbon::parse($sessioneAppello->data_fine)->lt(\Carbon\Carbon::today()) : false;
                                        @endphp
                                        <tr>
                                            <!-- Insegnamento -->
                                            <td class="py-3 px-4 fw-semibold text-dark">
                                                {{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }}
                                            </td>

                                            <!-- Data e Orario in Badge -->
                                            <td class="py-3 px-4 text-center text-nowrap">
                                                <span class="badge border border-unibs text-unibs bg-transparent px-3 py-2 rounded-3 fw-bold d-inline-block text-start">
                                                    <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                    <br>
                                                    <i class="bi bi-clock me-1"></i>{{ substr($appello->ora_inizio, 0, 5) }} - {{ substr($appello->ora_fine, 0, 5) }}
                                                </span>
                                            </td>

                                            <!-- Sede (Edificio e Aula in Badge) -->
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

                                            <!-- Pulsanti Azione -->
                                            <td class="py-3 px-4 text-center text-nowrap">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if($isPassata)
                                                        <!-- Modifica Disabilitata -->
                                                        <button type="button" 
                                                                class="btn bg-white border border-secondary text-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center opacity-50" 
                                                                style="width: 36px; height: 36px;" 
                                                                disabled 
                                                                title="Non è possibile modificare appelli di sessioni passate">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>

                                                        <!-- Elimina Disabilitato -->
                                                        <button type="button" 
                                                                class="btn bg-white border border-secondary text-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center opacity-50" 
                                                                style="width: 36px; height: 36px;" 
                                                                disabled 
                                                                title="Non è possibile eliminare appelli di sessioni passate">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @else
                                                        <!-- Modifica Attiva -->
                                                        <a href="{{ route('admin.appelli.edit', $appello->getKey()) }}" 
                                                           class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                           style="width: 36px; height: 36px;" 
                                                           title="Modifica Appello">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>

                                                        <!-- Elimina Attivo -->
                                                        <form action="{{ route('admin.appelli.destroy', $appello->getKey()) }}" 
                                                              method="POST" 
                                                              onsubmit="return confirm('Sei sicuro di voler eliminare questo appello?');" 
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
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                                        @if($sessione_id && $corso_id)
                                            Nessun appello fissato per la sessione ed il corso selezionati.
                                        @else
                                            Seleziona una sessione ed un corso di laurea per visualizzare il calendario.
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </body>
</html>