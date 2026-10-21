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
                
                <!-- Intestazione Pagina con Pulsante a Destra -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                    <div>
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-calendar-event-fill me-2"></i>Gestione Appelli Insegnamento
                        </h1>
                        <p class="text-muted small m-0 mt-1 fw-medium">
                            <strong>Materia:</strong> {{ $insegnamentoCorso->insegnamento->nome }} | 
                            <strong>Corso:</strong> {{ $insegnamentoCorso->corso->nome }} ({{ $insegnamentoCorso->anno_di_corso }}° Anno) | 
                            <strong>Ruolo:</strong> {{ $docenteInsegnamento->ruolo ?? 'Docente' }}
                        </p>
                    </div>

                    <!-- Pulsante Torna alla Dashboard -->
                    <a href="{{ route('docente.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase text-nowrap">
                        <i class="bi bi-arrow-left me-2"></i>TORNA ALLA DASHBOARD
                    </a>
                </div>

                <hr class="border-unibs border-2 opacity-100 mb-4">

                <!-- Alert Messaggi di Successo/Errore -->
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

                <!-- FILTRO OPZIONALE PER ANNO SESSIONE -->
                @php
                    $anniDisponibili = $sessioni->pluck('anno')->unique()->sort()->values();
                @endphp

                @if($anniDisponibili->count() > 0)
                    <div class="card shadow-sm border-0 rounded-3 mb-4 p-3 bg-white">
                        <div class="row align-items-center g-2">
                            <div class="col-12 col-md-auto fw-bold text-unibs d-flex align-items-center">
                                <i class="bi bi-funnel-fill me-2"></i>Filtra per Anno Sessione:
                            </div>
                            <div class="col-12 col-md-3">
                                <select id="filtroAnnoSessione" class="form-select form-select-sm shadow-sm border-unibs fw-semibold text-dark">
                                    <option value="tutti" selected>Tutti gli anni (Mostra tutto)</option>
                                    @foreach($anniDisponibili as $anno)
                                        <option value="{{ $anno }}">Anno {{ $anno }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-auto text-muted small ms-md-auto">
                                <i class="bi bi-info-circle me-1"></i> Seleziona un anno per restringere le sessioni visualizzate
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Avviso in caso nessun risultato corrisponda al filtro -->
                <div id="noSessioniMatchFilter" class="card shadow-sm border-0 rounded-3 p-5 text-center text-muted d-none mb-4 bg-white">
                    <i class="bi bi-funnel fs-2 text-secondary mb-2"></i>
                    <h6 class="fw-bold m-0">Nessuna sessione trovata per l'anno selezionato.</h6>
                    <small>Seleziona "Tutti gli anni" per mostrare nuovamente tutte le sessioni d'esame.</small>
                </div>

                <!-- SESSIONI ESISTENTI -->
                @foreach($sessioni as $sessione)
                    @php
                        $dataInizioSessione = \Carbon\Carbon::parse($sessione->data_inizio);
                        $isTermineSuperato = \Carbon\Carbon::today()->greaterThanOrEqualTo($dataInizioSessione->copy()->subDays(30));
                        $isPrimario = ($docenteInsegnamento->ruolo ?? 'Secondario') === 'Primario';

                        // Controllo del limite massimo di appelli
                        $numAppelliAttuali = count($sessione->appelli_insegnamento);
                        $limiteRaggiunto = $numAppelliAttuali >= $sessione->numero_appelli;
                    @endphp

                    <div class="card sessione-card shadow-sm border-0 rounded-3 overflow-hidden mb-4" data-anno="{{ $sessione->anno }}">
                        
                        <!-- HEADER SCHEDA SESSIONE -->
                        <div class="card-header bg-unibs text-white px-4 py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2 fs-5"></i> {{ $sessione->nome }} ({{ $sessione->anno }})
                                </h5>
                                <span class="small text-white-50 fw-medium">
                                    Dal {{ $dataInizioSessione->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}
                                    · <strong class="text-white">Max {{ $sessione->numero_appelli }} Appelli</strong> (Inseriti: {{ $numAppelliAttuali }})
                                </span>
                            </div>

                            <div>
                                @if(!$isPrimario)
                                    <!-- DOCENTE SECONDARIO: Mostra solo "Utente non abilitato" -->
                                    <span class="badge bg-white text-secondary opacity-75 fw-semibold px-3 py-2 rounded-3 shadow-sm">
                                        Utente non abilitato
                                    </span>
                                @elseif($isTermineSuperato)
                                    <!-- Bottone Disabilitato per Scadenza Termini (30 giorni) -->
                                    <button type="button" 
                                            class="btn btn-light text-secondary btn-sm fw-bold rounded-3 shadow-sm px-3 py-1.5 opacity-50 d-inline-flex align-items-center" 
                                            disabled 
                                            title="La sessione è passata o mancano meno di 30 giorni al suo inizio">
                                        <i class="bi bi-clock-history me-1.5"></i> Termini di modifica superati
                                    </button>
                                @elseif($limiteRaggiunto)
                                    <!-- Bottone Disabilitato per Limite Appelli Raggiunto -->
                                    <button type="button" 
                                            class="btn btn-light text-secondary btn-sm fw-bold rounded-3 shadow-sm px-3 py-1.5 opacity-50 d-inline-flex align-items-center" 
                                            disabled 
                                            title="Raggiunto il numero massimo di appelli consentiti per questa sessione">
                                        <i class="bi bi-exclamation-circle me-1.5"></i> Limite appelli raggiunto ({{ $numAppelliAttuali }}/{{ $sessione->numero_appelli }})
                                    </button>
                                @else
                                    <!-- DOCENTE PRIMARIO: Pulsante Attivo per Creazione Appello -->
                                    <a href="{{ route('docente.appelli.create', ['ins_corso_id' => $insegnamentoCorso->ins_corso_id, 'sessione_id' => $sessione->sessione_id]) }}" 
                                       class="btn btn-light text-unibs btn-sm fw-bold rounded-3 shadow-sm px-3 py-1.5 d-inline-flex align-items-center">
                                        <i class="bi bi-plus-circle-fill me-1.5 text-unibs"></i> Aggiungi Appello
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- TABELLA APPELLI DELLA SESSIONE -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            <th class="ps-4 py-3 text-unibs" style="width: 25%;">Data & Orario</th>
                                            <th class="py-3 text-unibs" style="width: 30%;">Insegnamento</th>
                                            <th class="py-3 text-unibs" style="width: 25%;">Sede d'Esame</th>
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

                                                <!-- Insegnamento -->
                                                <td class="py-3 text-dark">
                                                    <div class="fw-semibold text-dark">{{ $insegnamentoCorso->insegnamento->nome }}</div>
                                                    <div class="small text-muted fst-italic">
                                                        <i class="bi bi-mortarboard me-1 text-unibs"></i>{{ $insegnamentoCorso->corso->nome }}
                                                    </div>
                                                </td>

                                                <!-- Sede d'Esame -->
                                                <td class="py-3 text-dark">
                                                    <div class="fw-semibold text-dark">
                                                        <i class="bi bi-geo-alt-fill text-unibs me-1"></i>{{ $appello->aula->nome ?? 'Aula N/D' }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        {{ $appello->aula->edificio->nome ?? 'Edificio N/D' }}
                                                    </div>
                                                </td>

                                                <!-- Azioni -->
                                                <td class="pe-4 py-3 text-end text-nowrap">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        @if(!$isTermineSuperato && $isPrimario)
                                                            <!-- Pulsanti Attivi (Modifica & Elimina) -->
                                                            <a href="{{ route('docente.appelli.edit', $appello->appello_id) }}" 
                                                               class="btn btn-white text-unibs border border-unibs rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                               style="width: 32px; height: 32px;" 
                                                               title="Modifica Appello">
                                                                <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                                            </a>

                                                            <form action="{{ route('docente.appelli.destroy', $appello->appello_id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline m-0 p-0"
                                                                  data-confirm="Sei sicuro di voler eliminare questo appello d'esame?">
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
                                                            <!-- Pulsanti Disabilitati -->
                                                            <button type="button" 
                                                                    class="btn btn-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50" 
                                                                    disabled 
                                                                    style="width: 32px; height: 32px;" 
                                                                    title="{{ $isTermineSuperato ? 'Termini di modifica superati (mancano meno di 30 giorni)' : 'Azione riservata al docente primario' }}">
                                                                <i class="bi bi-pencil-fill" style="font-size: 0.85rem;"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50" 
                                                                    disabled 
                                                                    style="width: 32px; height: 32px;" 
                                                                    title="{{ $isTermineSuperato ? 'Termini di modifica superati (mancano meno di 30 giorni)' : 'Azione riservata al docente primario' }}">
                                                                <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted fw-medium">
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

            </div>
        </div>

        <!-- Script Filtraggio Dinamico per Anno Sessione -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterSelect = document.getElementById('filtroAnnoSessione');
                if (!filterSelect) return;

                const sessionCards = document.querySelectorAll('.sessione-card');
                const noMatchAlert = document.getElementById('noSessioniMatchFilter');

                filterSelect.addEventListener('change', () => {
                    const selectedAnno = filterSelect.value;
                    let visibleCount = 0;

                    sessionCards.forEach(card => {
                        const cardAnno = card.getAttribute('data-anno');
                        if (selectedAnno === 'tutti' || cardAnno === selectedAnno) {
                            card.classList.remove('d-none');
                            visibleCount++;
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    if (visibleCount === 0) {
                        noMatchAlert.classList.remove('d-none');
                    } else {
                        noMatchAlert.classList.add('d-none');
                    }
                });
            });
        </script>

        <!-- Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>