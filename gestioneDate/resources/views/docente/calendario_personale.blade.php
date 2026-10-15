<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendario Personale - Docente</title>

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
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        
                        <!-- Titolo Intestazione Docente -->
                        <div class="mb-4">
                            <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                                <i class="bi bi-calendar-check-fill me-2"></i>Calendario Personale Appelli
                            </h1>
                            <p class="text-dark mb-2 small">
                                Docente: <strong>Prof. {{ Auth::user()->nome }} {{ Auth::user()->cognome }}</strong>
                            </p>
                            <hr class="border-unibs border-2 opacity-100 my-0">
                        </div>

                        <!-- Card Principale Calendario Appelli -->
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                            
                            <!-- Intestazione Card con Nome e Anno della Sessione -->
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar-event-fill me-2 fs-5"></i> Appelli Programmati: {{ $sessione->nome }} ({{ $sessione->anno }})
                                </h5>
                                <!--<a href="{{ route('appelli.create') }}" class="btn btn-light btn-sm text-unibs fw-bold px-3 py-1 rounded-pill shadow-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Nuovo Appello
                                </a>-->
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light border-bottom">
                                            <tr>
                                                <th class="ps-4 py-3 text-unibs fw-bold">Insegnamento</th>
                                                <th class="py-3 text-unibs fw-bold">Corso di Laurea</th>
                                                <th class="py-3 text-unibs fw-bold text-center">Data</th>
                                                <th class="py-3 text-unibs fw-bold text-center">Orario</th>
                                                <th class="pe-4 py-3 text-unibs fw-bold text-end">Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hasAppelli = false; @endphp

                                            @foreach($insegnamentiCorsi as $insC)
                                                @foreach($insC->appelli as $appello)
                                                    @php $hasAppelli = true; @endphp
                                                    <tr>
                                                        <td class="ps-4 py-3 fw-bold text-dark">
                                                            {{ $insC->insegnamento->nome }}
                                                        </td>
                                                        <td class="py-3 text-dark fw-medium">
                                                            {{ $insC->corso->nome }}
                                                        </td>
                                                        <td class="py-3 text-center">
                                                            <span class="badge bg-white text-unibs border border-unibs fw-bold px-2.5 py-1.5 rounded-pill shadow-sm">
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                {{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 text-center text-dark fw-medium">
                                                            <i class="bi bi-clock me-1 text-dark"></i>
                                                            {{ \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($appello->ora_fine)->format('H:i') }}
                                                        </td>
                                                        <td class="text-end pe-4 py-3">
                                                            <div class="btn-group shadow-sm" role="group" aria-label="Azioni appello">
                                                                <!-- Pulsante Modifica -->
                                                                <a href="{{ route('appelli.edit', $appello->appello_id) }}" 
                                                                   class="btn btn-unibs text-white fw-semibold d-inline-flex flex-column align-items-center justify-content-center py-1.5" 
                                                                   style="width: 80px;" 
                                                                   title="Modifica Appello">
                                                                    <i class="bi bi-pencil-square fs-6 mb-0.5"></i>
                                                                    <span style="font-size: 0.7rem;">Modifica</span>
                                                                </a>

                                                                <!-- Form e Pulsante Elimina -->
                                                                <form action="{{ route('appelli.destroy', $appello->appello_id) }}" 
                                                                      method="POST" 
                                                                      class="d-inline"
                                                                      onsubmit="return confirm('Sei sicuro di voler eliminare questo appello?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="btn btn-danger text-white fw-semibold d-inline-flex flex-column align-items-center justify-content-center py-1.5 h-100" 
                                                                            style="width: 80px; border-top-left-radius: 0; border-bottom-left-radius: 0;" 
                                                                            title="Elimina Appello">
                                                                        <i class="bi bi-trash fs-6 mb-0.5"></i>
                                                                        <span style="font-size: 0.7rem;">Elimina</span>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                            @if(!$hasAppelli)
                                                <tr>
                                                    <td colspan="5" class="text-center py-5 text-dark fw-bold">
                                                        <i class="bi bi-calendar-x fs-2 d-block mb-2 text-unibs"></i>
                                                        Nessun appello fissato per le tue materie in questa sessione.
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Bottoni di Navigazione Inferiori -->
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <a href="{{ route('docente.dashboard') }}" 
                                   class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-arrow-left me-2 fs-6"></i>Torna alla Dashboard
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="{{ route('docente.selezionaSessione') }}" 
                                   class="btn btn-unibs text-white fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-event me-2 fs-6"></i>Seleziona Un'Altra Sessione
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </body>
</html>