<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuovo Insegnamento - Amministratore</title>

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
            <div class="row justify-content-center">
                <div class="col-12 col-md-8">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-journal-plus me-2"></i>Aggiungi Nuovo Insegnamento
                        </h1>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                        </a>
                    </div>
                    <hr class="border-unibs border-2 opacity-100 mb-4">

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <form action="{{ route('admin.insegnamenti.store') }}" method="POST">
                                @csrf

                                <!-- Codice Insegnamento -->
                                <div class="mb-3">
                                    <label for="insegnamento_id" class="form-label fw-bold">Codice Insegnamento <span class="text-danger">*</span></label>
                                    <input type="text" name="insegnamento_id" id="insegnamento_id" class="form-control" placeholder="es. ING-INF/05" required>
                                </div>

                                <!-- Nome Insegnamento -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome Insegnamento <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" placeholder="es. Programmazione Web" required>
                                </div>

                                <!-- Sezione Corso di Laurea -->
                                <div class="mb-3">
                                    <label for="corso_id" class="form-label fw-bold">Corso di Laurea <span class="text-danger">*</span></label>
                                    <select name="corso_id" id="corso_id" class="form-select" required>
                                        <option value="" disabled {{ empty($selectedCorsoId) ? 'selected' : '' }}>-- Seleziona un corso --</option>
                                        @foreach($corsi as $c)
                                            <option value="{{ $c->getKey() }}" {{ (isset($selectedCorsoId) && $selectedCorsoId == $c->getKey()) ? 'selected' : '' }}>
                                                {{ $c->nome }} ({{ $c->tipo_corso }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sezione Anno di Corso -->
                                <div class="col-12 col-md-4">
                                    <label for="anno_di_corso" class="form-label fw-bold">Anno di Corso <span class="text-danger">*</span></label>
                                    <select name="anno_di_corso" id="anno_di_corso" class="form-select" required>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ (isset($selectedAnno) && $selectedAnno == $i) ? 'selected' : '' }}>
                                                {{ $i }}° Anno
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Dettagli: Anno, CFU, Durata (ore) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-4">
                                        <label for="anno_di_corso" class="form-label fw-bold">Anno di Corso <span class="text-danger">*</span></label>
                                        <select name="anno_di_corso" id="anno_di_corso" class="form-select" required>
                                            <option value="1">1° Anno</option>
                                            <option value="2">2° Anno</option>
                                            <option value="3">3° Anno</option>
                                            <option value="4">4° Anno</option>
                                            <option value="5">5° Anno</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="cfu" class="form-label fw-bold">CFU <span class="text-danger">*</span></label>
                                        <input type="number" name="cfu" id="cfu" class="form-control" min="1" max="30" placeholder="es. 6" required>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="durata" class="form-label fw-bold">Durata (Ore) <span class="text-danger">*</span></label>
                                        <input type="number" name="durata" id="durata" class="form-control" min="1" placeholder="es. 48" required>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Salva Insegnamento
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>
