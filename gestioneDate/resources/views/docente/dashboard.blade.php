<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Docente</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <!-- Script Bootstrap & Popper -->
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>

        <!-- Navbar Istituzionale UniBS -->
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
                               href="{{ route('docente.dashboard') }}" style="width: 115px;">
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
            <div class="container px-md-4 px-lg-5">
                
                <!-- Intestazione Profilo Docente -->
                <div class="mb-4 border-bottom border-2 border-unibs pb-2">
                    <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                        <i class="bi bi-journal-bookmark-fill me-2 fs-3"></i>
                        Docente: Prof. {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                    </h1>
                </div>

                <!-- Card Tabella Insegnamenti -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                    <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                            <i class="bi bi-journal-text me-2 fs-5"></i> Gestione Insegnamenti e Appelli
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="py-3 ps-4 text-unibs fw-bold" style="width: 32%;">Insegnamento</th>
                                        <th class="py-3 text-unibs fw-bold" style="width: 28%;">Corso di Studi</th>
                                        <th class="py-3 text-unibs fw-bold" style="width: 15%;">Ruolo</th>
                                        <th class="py-3 pe-4 text-center text-unibs fw-bold" style="width: 25%;">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($insegnamentiCorsi as $insC)
                                        <tr>
                                            <!-- Nome Insegnamento -->
                                            <td class="py-3 ps-4 fw-bold text-dark">
                                                {{ $insC->insegnamento->nome }}
                                            </td>

                                            <!-- Corso di Studi -->
                                            <td class="py-3 text-dark fw-medium">
                                                {{ $insC->corso->nome }}
                                            </td>

                                            <!-- Ruolo Docente -->
                                            <td class="py-3 text-dark">
                                                <span class="fw-semibold">
                                                    {{ $insC->ruolo_docente ?? 'Docente' }}
                                                </span>
                                            </td>

                                            <!-- Pulsanti Azioni Circolari con Etichetta Sotto -->
                                            <td class="py-3 pe-4 text-center text-nowrap">
                                                <div class="d-inline-flex align-items-center justify-content-center gap-4">
                                                    
                                                    <!-- Pulsante Circular VISUALIZZA -->
                                                    <a href="{{ route('docente.dettaglioInsegnamento', $insC->ins_corso_id) }}" 
                                                       class="text-decoration-none d-flex flex-column align-items-center group"
                                                       title="Visualizza Dettagli Insegnamento">
                                                        <div class="bg-white text-unibs border border-2 border-unibs rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                                             style="width: 38px; height: 38px;">
                                                            <i class="bi bi-eye-fill fs-6"></i>
                                                        </div>
                                                        <span class="fw-bold text-unibs mt-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">VISUALIZZA</span>
                                                    </a>

                                                    <!-- Pulsante Circular APPELLI -->
                                                    <a href="{{ route('docente.appelliInsegnamento', $insC->ins_corso_id) }}" 
                                                       class="text-decoration-none d-flex flex-column align-items-center group"
                                                       title="Gestisci Appelli per questo Insegnamento">
                                                        <div class="bg-white text-success border border-2 border-success rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                                             style="width: 38px; height: 38px;">
                                                            <i class="bi bi-calendar-plus-fill fs-6"></i>
                                                        </div>
                                                        <span class="fw-bold text-success mt-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">APPELLI</span>
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted fw-medium">
                                                <i class="bi bi-info-circle me-1 text-secondary"></i> Nessun insegnamento associato al tuo profilo.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Link Rapidi Inferiori -->
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <a href="{{ route('docente.selezionaSessione') }}" 
                           class="btn btn-white text-unibs bg-white border-0 fw-bold py-3 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-check-fill me-2 fs-5"></i> CALENDARIO PERSONALE
                        </a>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('docente.selezionaFiltriGenerale') }}" 
                           class="btn btn-white text-unibs bg-white border-0 fw-bold py-3 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar3 me-2 fs-5"></i> CALENDARIO GENERALE
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </body>
</html>