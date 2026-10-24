<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Importa da Excel</title>
        <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Importa da Excel</h1>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                        </a>
                    </div>

                    @if(!$libreriaOk)
                        <div class="alert alert-warning rounded-3 mb-3">
                            <strong>Libreria Excel non installata.</strong> Dalla cartella del progetto esegui
                            <code>composer require phpoffice/phpspreadsheet</code> (con le estensioni PHP <code>zip</code> e <code>gd</code> attive), poi ricarica la pagina.
                        </div>
                    @endif

                    {{-- Errori di validazione del form (file mancante, formato errato...) --}}
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="m-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Esito dell'ultima importazione --}}
                    @php $report = session('import_report'); @endphp
                    @if($report)
                        @if($report['ok'])
                            @php $r = $report['riepilogo']; @endphp
                            <div class="alert {{ $report['applicato'] ? 'alert-success' : 'alert-info' }} rounded-3 mb-3">
                                <div class="fw-bold mb-2">
                                    <i class="bi {{ $report['applicato'] ? 'bi-check-circle-fill' : 'bi-search' }} me-1"></i>
                                    @if($report['applicato'])
                                        Importazione completata
                                    @else
                                        Verifica superata: il file è corretto. Non è stato salvato nulla (deseleziona «Solo verifica» per importare).
                                    @endif
                                    <span class="fw-normal text-muted">({{ $report['file'] ?? '' }})</span>
                                </div>
                                <ul class="m-0 ps-3">
                                    <li><strong>Corsi:</strong> {{ $r['corsi']['creati'] }} {{ $report['applicato'] ? 'creati' : 'da creare' }}, {{ $r['corsi']['esistenti'] }} già presenti</li>
                                    <li><strong>Insegnamenti:</strong> {{ $r['insegnamenti']['creati'] }} {{ $report['applicato'] ? 'creati' : 'da creare' }}</li>
                                    <li><strong>Piani di studio:</strong> {{ $r['piano']['creati'] }} {{ $report['applicato'] ? 'aggiunti' : 'da aggiungere' }}, {{ $r['piano']['aggiornati'] }} {{ $report['applicato'] ? 'aggiornati' : 'da aggiornare' }}, {{ $r['piano']['invariati'] }} invariati</li>
                                    <li><strong>Associazioni docenti:</strong> {{ $r['associazioni']['create'] }} {{ $report['applicato'] ? 'create' : 'da creare' }}, {{ $r['associazioni']['aggiornate'] }} {{ $report['applicato'] ? 'aggiornate' : 'da aggiornare' }}, {{ $r['associazioni']['invariate'] }} invariate</li>
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-danger rounded-3 mb-3">
                                <div class="fw-bold mb-2">
                                    <i class="bi bi-x-octagon-fill me-1"></i>
                                    Importazione annullata: nel file ci sono errori. Non è stato salvato nulla.
                                    <span class="fw-normal">({{ $report['file'] ?? '' }})</span>
                                </div>
                                <ul class="m-0 ps-3">
                                    @foreach($report['errori'] as $errore)
                                        <li>{{ $errore }}</li>
                                    @endforeach
                                </ul>
                                @if(($report['errori_omessi'] ?? 0) > 0)
                                    <div class="mt-2 small">… e altri {{ $report['errori_omessi'] }} errori non mostrati. Correggi questi e riprova.</div>
                                @endif
                            </div>
                        @endif
                    @endif

                    <div class="row g-3">
                        {{-- 1. Modello --}}
                        <div class="col-12 col-md-5">
                            <div class="card shadow-sm border-0 rounded-3 h-100">
                                <div class="card-body p-4 d-flex flex-column">
                                    <h2 class="h6 fw-bold text-unibs mb-2">1. Scarica il modello</h2>
                                    <p class="small text-muted">File Excel con i fogli già pronti (<strong>Corsi</strong>, <strong>Piano di studi</strong>, <strong>Associazioni docenti</strong>) e un foglio di istruzioni. Compila solo i fogli che ti servono.</p>
                                    <a href="{{ route('admin.import.modello') }}" class="btn btn-unibs text-white fw-bold rounded-pill shadow-sm mt-auto">
                                        <i class="bi bi-download me-1"></i>Scarica il modello
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Caricamento --}}
                        <div class="col-12 col-md-7">
                            <div class="card shadow-sm border-0 rounded-3 h-100">
                                <div class="card-body p-4">
                                    <h2 class="h6 fw-bold text-unibs mb-2">2. Carica il file compilato</h2>
                                    <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="file" name="file" class="form-control" accept=".xlsx" required>
                                            <div class="form-text">Formato .xlsx, massimo 5 MB.</div>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="solo_verifica" id="solo_verifica" value="1" {{ old('solo_verifica') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="solo_verifica">Solo verifica (controlla il file senza salvare)</label>
                                        </div>
                                        <button type="submit" class="btn btn-unibs text-white fw-bold rounded-pill px-4 shadow-sm">
                                            <i class="bi bi-upload me-1"></i>Importa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Regole --}}
                        <div class="col-12">
                            <div class="card shadow-sm border-0 rounded-3">
                                <div class="card-body p-4">
                                    <h2 class="h6 fw-bold text-unibs mb-3">Come funziona</h2>
                                    <ul class="small mb-0 ps-3">
                                        <li>Il sistema controlla <strong>tutte</strong> le righe prima di salvare: se c'è anche un solo errore non viene salvato nulla e vedrai l'elenco con foglio e riga.</li>
                                        <li><strong>Tipo corso</strong>: {{ implode(', ', $tipi) }}. <strong>Ruolo</strong>: {{ implode(', ', $ruoli) }}.</li>
                                        <li><strong>Anno di corso</strong>: da 1 a 3 (Triennale), da 1 a 2 (Magistrale), da 1 a 5 (Magistrale a ciclo unico). <strong>CFU</strong>: da 1 a 30. <strong>Ore</strong>: ore di lezione.</li>
                                        <li>I docenti devono essere già registrati (colonna <strong>Username docente</strong>, ruolo Docente). Un solo Primario per insegnamento; un docente non può essere Primario di due insegnamenti dello stesso anno dello stesso corso.</li>
                                        <li>Puoi reimportare lo stesso file: ciò che esiste già non viene duplicato; anno, CFU e ore di un insegnamento già nel piano vengono aggiornati.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>
