<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestione Appelli - {{ $insegnamentoCorso->insegnamento->nome }}</title>

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
            <div class="container-fluid px-md-4 px-lg-5">
                
                <!-- Intestazione Pagina -->
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs mb-1 d-flex align-items-center">
                            <i class="bi bi-calendar-event-fill me-2"></i>Gestione Appelli Insegnamento
                        </h1>
                        <p class="text-muted mb-0 fw-medium">
                            <strong>Materia:</strong> {{ $insegnamentoCorso->insegnamento->nome }} | 
                            <strong>Corso:</strong> {{ $insegnamentoCorso->corso->nome }} ({{ $insegnamentoCorso->anno_di_corso }}° Anno) | 
                            <strong>Ruolo:</strong> {{ $docenteInsegnamento->ruolo ?? 'Docente' }}
                        </p>
                    </div>

                    <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-unibs fw-bold rounded-3 shadow-sm px-3 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Torna alla Dashboard
                    </a>
                </div>

                <!-- Ciclo sulle N Sessioni Esistenti (Ordinate Cronologicamente) -->
                @foreach($sessioni as $sessione)
                    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                        
                        <!-- Header Card della Sessione -->
                        <div class="card-header bg-unibs text-white px-4 py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2 fs-5"></i> {{ $sessione->nome }} ({{ $sessione->anno }})
                                </h5>
                                <span class="small text-white-50 fw-medium">
                                    Dal {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}
                                </span>
                            </div>

                            <!-- Pulsante Aggiungi Appello per questa specifica Sessione -->
                            <div>
                                @if(($docenteInsegnamento->ruolo ?? 'Secondario') === 'Primario')
                                    <a href="{{ route('appelli.create', ['ins_corso_id' => $insegnamentoCorso->ins_corso_id, 'sessione_id' => $sessione->sessione_id]) }}" 
                                       class="btn btn-light text-unibs btn-sm fw-bold rounded-3 shadow-sm px-3 py-1.5 d-inline-flex align-items-center">
                                        <i class="bi bi-plus-circle-fill me-1.5 text-unibs"></i> Aggiungi Appello
                                    </a>
                                @else
                                    <button type="button" 
                                            class="btn btn-light text-secondary btn-sm fw-bold rounded-3 shadow-sm px-3 py-1.5 opacity-50 d-inline-flex align-items-center" 
                                            disabled 
                                            title="Azione riservata al docente primario dell'insegnamento">
                                        <i class="bi bi-slash-circle me-1.5"></i> Aggiungi Appello
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Tabella Appelli Programmati per la Sessione -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            <th class="ps-4 py-3 text-unibs" style="width: 35%;">Data & Orario</th>
                                            <th class="py-3 text-unibs" style="width: 45%;">Insegnamento</th>
                                            <th class="pe-4 py-3 text-unibs text-end" style="width: 20%;">Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sessione->appelli_insegnamento as $appello)
                                            <tr>
                                                <!-- Data & Orario -->
                                                <td class="ps-4 py-3 text-dark fs-7">
                                                    <div class="fw-bold">
                                                        <i class="bi bi-calendar-event text-unibs me-1"></i>
                                                        {{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                    </div>
                                                    <div class="small text-muted fw-medium mt-0.5">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i') }} - {{ \Carbon\Carbon::parse($appello->ora_fine)->format('H:i') }}
                                                    </div>
                                                </td>

                                                <!-- Insegnamento e Corso -->
                                                <td class="py-3 text-dark">
                                                    <div class="fw-semibold text-dark">{{ $insegnamentoCorso->insegnamento->nome }}</div>
                                                    <div class="small text-muted fst-italic">
                                                        <i class="bi bi-mortarboard me-1 text-unibs"></i>{{ $insegnamentoCorso->corso->nome }}
                                                    </div>
                                                </td>

                                                <!-- Pulsanti Modifica / Elimina Minimali -->
                                                <td class="pe-4 py-3 text-end text-nowrap">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        @if(($docenteInsegnamento->ruolo ?? 'Secondario') === 'Primario')
                                                            <!-- Modifica - Abilitato -->
                                                            <a href="{{ route('appelli.edit', $appello->appello_id) }}" 
                                                               class="btn btn-white text-unibs border border-unibs rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                               style="width: 32px; height: 32px;" 
                                                               title="Modifica Appello">
                                                                <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                                            </a>

                                                            <!-- Elimina - Abilitato -->
                                                            <form action="{{ route('appelli.destroy', $appello->appello_id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline m-0 p-0"
                                                                  onsubmit="return confirm('Sei sicuro di voler eliminare questo appello?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="btn btn-white text-danger border border-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                                        style="width: 32px; height: 32px;" 
                                                                        title="Elimina Appello">
                                                                    <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <!-- Modifica - Disabilitato -->
                                                            <button type="button" 
                                                                    class="btn btn-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50" 
                                                                    disabled 
                                                                    style="width: 32px; height: 32px; cursor: not-allowed;"
                                                                    title="Azione riservata al docente primario dell'insegnamento">
                                                                <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                                            </button>

                                                            <!-- Elimina - Disabilitato -->
                                                            <button type="button" 
                                                                    class="btn btn-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50" 
                                                                    disabled 
                                                                    style="width: 32px; height: 32px; cursor: not-allowed;" 
                                                                    title="Azione riservata al docente primario dell'insegnamento">
                                                                <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted fw-medium">
                                                    <i class="bi bi-info-circle me-1 text-secondary"></i> Nessun appello fissato per questo insegnamento in questa sessione.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                @endforeach

                <!-- Pulsante Inferiore A Tutta Larghezza -->
                <div class="row mt-4">
                    <div class="col-12">
                        <a href="{{ route('docente.dashboard') }}" 
                           class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-arrow-left me-2 fs-6"></i>Torna alla Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </body>
</html>