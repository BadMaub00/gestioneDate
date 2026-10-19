<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aggiungi Insegnamento al Corso - {{ $corso->nome }}</title>

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
                        data-bs-target="#navbarSupportedContent">
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
        <div class="container py-3" style="max-width: 900px;">
            <div class="row justify-content-center">
                <div class="col-12">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                                <i class="bi bi-journal-plus me-2"></i>Aggiungi Insegnamento
                            </h1>
                            <p class="text-muted small m-0 mt-1">
                                Corso: <strong>{{ $corso->nome }}</strong> 
                                <span class="badge bg-unibs text-white ms-1">{{ $corso->tipo_corso }} - {{ $durataAnni }} {{ $durataAnni == 1 ? 'Anno' : 'Anni' }}</span>
                            </p>
                        </div>
                        <a href="{{ route('admin.corsi.pianoDiStudi', $corso->corso_id) }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna al Piano di Studi
                        </a>
                    </div>

                    <hr class="border-unibs border-2 opacity-100 mb-4">

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <!-- Visualizzazione Errori di Validazione -->
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.corsi.insegnamenti.store', $corso->corso_id) }}" method="POST">
                                @csrf

                                <!-- Insegnamento -->
                                <div class="mb-3">
                                    <label for="insegnamento_id" class="form-label fw-bold">Insegnamento <span class="text-danger">*</span></label>
                                    <select name="insegnamento_id" id="insegnamento_id" class="form-select @error('insegnamento_id') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('insegnamento_id') ? '' : 'selected' }}>-- Seleziona un Insegnamento --</option>
                                        @foreach($insegnamenti as $ins)
                                            <option value="{{ $ins->insegnamento_id }}" {{ old('insegnamento_id') == $ins->insegnamento_id ? 'selected' : '' }}>
                                                {{ $ins->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Anno di Corso, CFU e Durata -->
                                <div class="row g-3 mb-4">
                                    
                                    <!-- Selezione Anno limitata a $durataAnni (3 per Triennale, 2 per Magistrale) -->
                                    <div class="col-12 col-md-4">
                                        <label for="anno_di_corso" class="form-label fw-bold">Anno di Corso <span class="text-danger">*</span></label>
                                        <select name="anno_di_corso" id="anno_di_corso" class="form-select @error('anno_di_corso') is-invalid @enderror" required>
                                            @for($i = 1; $i <= $durataAnni; $i++)
                                                <option value="{{ $i }}" {{ old('anno_di_corso') == $i ? 'selected' : '' }}>
                                                    {{ $i }}° Anno
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- CFU -->
                                    <div class="col-12 col-md-4">
                                        <label for="cfu" class="form-label fw-bold">CFU <span class="text-danger">*</span></label>
                                        <input type="number" name="cfu" id="cfu" class="form-control @error('cfu') is-invalid @enderror" min="1" max="30" value="{{ old('cfu') }}" placeholder="es. 6" required>
                                    </div>

                                    <!-- Durata in Ore -->
                                    <div class="col-12 col-md-4">
                                        <label for="durata" class="form-label fw-bold">Durata (Ore) <span class="text-danger">*</span></label>
                                        <input type="number" name="durata" id="durata" class="form-control @error('durata') is-invalid @enderror" min="1" value="{{ old('durata') }}" placeholder="es. 48" required>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Salva nel Piano di Studi
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