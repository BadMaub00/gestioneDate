<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Appello - Amministratore</title>

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
            <div class="row justify-content-center">
                <div class="col-12 col-md-7">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-pencil-square me-2"></i>Modifica Appello d'Esame
                        </h1>
                        <a href="{{ route('admin.calendario', ['sessione_id' => $appello->sessione_id, 'corso_id' => $appello->insegnamentoCorso->corso_id ?? null]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i>Annulla
                        </a>
                    </div>
                    <hr class="border-unibs border-2 opacity-100 mb-4">

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <!-- Messaggi di errore globale -->
                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.appelli.update', $appello->getKey()) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Insegnamento e Corso (Solo lettura) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Insegnamento / Corso</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="{{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }} ({{ $appello->insegnamentoCorso->corso->nome ?? 'N/D' }})" disabled>
                                </div>

                                <!-- Data dell'Appello -->
                                <div class="mb-3">
                                    <label for="data" class="form-label fw-bold">Data Esame <span class="text-danger">*</span></label>
                                    <input type="date" name="data" id="data" class="form-control" value="{{ old('data', $appello->data) }}" required>
                                </div>

                                <!-- Orari Inizio e Fine -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label for="ora_inizio" class="form-label fw-bold">Ora Inizio <span class="text-danger">*</span></label>
                                        <input type="time" name="ora_inizio" id="ora_inizio" class="form-control" value="{{ old('ora_inizio', $appello->ora_inizio) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="ora_fine" class="form-label fw-bold">Ora Fine <span class="text-danger">*</span></label>
                                        <input type="time" name="ora_fine" id="ora_fine" class="form-control" value="{{ old('ora_fine', $appello->ora_fine) }}" required>
                                    </div>
                                </div>

                                <!-- Selezione Edificio -->
                                <div class="mb-3">
                                    <label for="edificio_id" class="form-label fw-bold">Edificio <span class="text-danger">*</span></label>
                                    <select name="edificio_id" id="edificio_id" class="form-select" required>
                                        <option value="" disabled {{ old('edificio_id', $appello->aula->edificio_id ?? null) ? '' : 'selected' }}>-- Seleziona Edificio --</option>
                                        @foreach($edifici as $edificio)
                                            <option value="{{ $edificio->edificio_id }}" 
                                                {{ old('edificio_id', $appello->aula->edificio_id ?? null) == $edificio->edificio_id ? 'selected' : '' }}>
                                                {{ $edificio->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Selezione Aula (Con Capienza e filtrata dinamicamente via JS) -->
                                <div class="mb-4">
                                    <label for="aula_id" class="form-label fw-bold">Aula <span class="text-danger">*</span></label>
                                    <select name="aula_id" id="aula_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona prima un Edificio --</option>
                                        @foreach($aule as $aula)
                                            <option value="{{ $aula->aula_id }}" 
                                                    data-edificio-id="{{ $aula->edificio_id }}"
                                                    {{ old('aula_id', $appello->aula_id) == $aula->aula_id ? 'selected' : '' }}>
                                                {{ $aula->nome }} (Capienza: {{ $aula->capienza ?? 'N/D' }} posti)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Salva Modifiche
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Script per Cascading Dropdown (Edificio -> Aula) -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const edificioSelect = document.getElementById('edificio_id');
                const aulaSelect = document.getElementById('aula_id');
                const aulaOptions = Array.from(aulaSelect.querySelectorAll('option[data-edificio-id]'));
                const currentAulaId = "{{ old('aula_id', $appello->aula_id) }}";

                function filterAule() {
                    const selectedEdificioId = edificioSelect.value;
                    
                    // Svuota le opzioni mantenendo solo il placeholder
                    aulaSelect.innerHTML = '<option value="" disabled selected>-- Seleziona Aula --</option>';

                    if (!selectedEdificioId) return;

                    // Filtra e ripopola le aule appartenenti all'edificio selezionato
                    const filteredAule = aulaOptions.filter(opt => opt.getAttribute('data-edificio-id') === selectedEdificioId);

                    filteredAule.forEach(opt => {
                        const newOpt = opt.cloneNode(true);
                        if (newOpt.value === currentAulaId) {
                            newOpt.selected = true;
                        }
                        aulaSelect.appendChild(newOpt);
                    });
                }

                edificioSelect.addEventListener('change', function () {
                    // Reset selezione dell'aula alla modifica dell'edificio
                    filterAule();
                });

                // Inizializza il filtro al caricamento della pagina
                filterAule();
            });
        </script>

    </body>
</html>