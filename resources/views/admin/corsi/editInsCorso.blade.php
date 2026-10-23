<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Insegnamento Corso - {{ $corso->nome }}</title>

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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Si sono verificati degli errori durante la compilazione del modulo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i>Modifica Insegnamento
                    </h1>
                    <span class="badge border border-unibs text-unibs bg-transparent rounded-pill px-3 py-1.5 fw-bold mt-1">
                        {{ $corso->nome }} ({{ $corso->tipo_corso }})
                    </span>
                </div>
                <a href="{{ route('admin.corsi.piano_di_studi', $corso->corso_id) }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Torna al Piano di Studi
                </a>
            </div>
            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Card Form di Modifica -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-unibs text-white fw-bold py-2">
                    <i class="bi bi-journal-text me-2"></i>Parametri Insegnamento
                </div>
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('admin.corsi.insegnamenti.update', $insegnamentoCorso->ins_corso_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Insegnamento (Solo Lettura) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-unibs">Insegnamento</label>
                            <input type="text" class="form-control bg-light text-muted" 
                                   value="{{ $insegnamentoCorso->insegnamento->nome ?? 'N/D' }} (Codice: {{ $insegnamentoCorso->insegnamento_id }})" disabled readonly>
                        </div>

                        <!-- Anno di corso -->
                        <div class="mb-3">
                            <label for="anno_di_corso" class="form-label fw-bold text-unibs">Anno di Corso</label>
                            <select name="anno_di_corso" id="anno_di_corso" class="form-select @error('anno_di_corso') is-invalid @enderror" required>
                                @for($a = 1; $a <= $durataAnni; $a++)
                                    <option value="{{ $a }}" {{ old('anno_di_corso', $insegnamentoCorso->anno_di_corso) == $a ? 'selected' : '' }}>
                                        {{ $a }}° Anno
                                    </option>
                                @endfor
                            </select>
                            @error('anno_di_corso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CFU -->
                        <div class="mb-3">
                            <label for="cfu" class="form-label fw-bold text-unibs">CFU</label>
                            <input type="number" name="cfu" id="cfu" class="form-control @error('cfu') is-invalid @enderror" 
                                   value="{{ old('cfu', $insegnamentoCorso->cfu) }}" min="1" max="30" required>
                            @error('cfu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ore / Durata -->
                        <div class="mb-4">
                            <label for="durata" class="form-label fw-bold text-unibs">Ore di Lezione (Durata)</label>
                            <input type="number" name="durata" id="durata" class="form-control @error('durata') is-invalid @enderror" 
                                   value="{{ old('durata', $insegnamentoCorso->durata) }}" min="1" required>
                            @error('durata')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bottoni Azione -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.corsi.piano_di_studi', $corso->corso_id) }}" class="btn btn-secondary rounded-pill px-4">Annulla</a>
                            <button type="submit" class="btn btn-unibs text-white fw-bold rounded-pill px-4">
                                <i class="bi bi-check-circle-fill me-1"></i>Salva Modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html>