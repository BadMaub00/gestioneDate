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
        <link rel="stylesheet" href="{{ asset('css/docente.css') }}">
    </head>
    <body class="bg-light min-vh-100">

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
                               href="{{ route('docente.dashboard') }}"
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
        <div class="py-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        
                        <!-- Titolo Intestazione Docente Dinamico -->
                        <div class="mb-4">
                            <h1 class="h3 fw-bold text-unibs mt-3 mb-2 d-flex align-items-center">
                                <i class="bi bi-person-badge me-2"></i>Docente: Prof. {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                            </h1>
                            <hr class="border-unibs border-2 opacity-100 my-0">
                        </div>

                        <!-- Card Gestione Insegnamenti -->
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            
                            <!-- Intestazione Card Blu Istituzionale -->
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-journal-bookmark-fill me-2 fs-4"></i> Gestione Insegnamenti e Appelli
                                </h5>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <tbody>
                                            @forelse($insegnamentiCorsi as $insC)
                                                <tr>
                                                    <td class="ps-4 py-3 fw-bold text-dark" style="width: 45%;">
                                                        {{ $insC->insegnamento->nome }}
                                                    </td>
                                                    <td class="py-3 text-dark fw-medium" style="width: 35%;">
                                                        {{ $insC->corso->nome }}
                                                    </td>
                                                    <td class="text-end pe-4 py-3" style="width: 20%;">
                                                        <div class="btn-group shadow-sm" role="group" aria-label="Azioni insegnamento">
                                                            <a href="#" class="btn btn-unibs text-white fw-semibold d-inline-flex flex-column align-items-center justify-content-center py-2" style="width: 90px;">
                                                                <i class="bi bi-eye fs-5 mb-1"></i>
                                                                <span style="font-size: 0.75rem;">Visualizza</span>
                                                            </a>
                                                            <a href="#" class="btn btn-success text-white fw-semibold d-inline-flex flex-column align-items-center justify-content-center py-2" style="width: 90px;">
                                                                <i class="bi bi-calendar-event fs-5 mb-1"></i>
                                                                <span style="font-size: 0.75rem;">Appelli</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted fw-bold">
                                                        Nessun insegnamento associato a questo docente.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Bottoni Calendari a tutta larghezza (50% ciascuno) -->
                        <div class="row g-2 mt-3">
                            <div class="col-12 col-md-6">
                                <a href="{{ route('docente.selezionaSessione') }}" 
                                class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-check-fill me-2 fs-6"></i>Calendario Personale
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="calendarioGenerale.html" class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar3 me-2 fs-6"></i>Calendario Generale
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </body>
</html>