<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Calendario Generale Appelli - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <script src="{{ asset('js/confirmDelete.js') }}"></script>

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            
            <x-flash-messages />

            <!-- Intestazione -->
            <x-page-header icon="calendar-check-fill" titolo="Calendario Generale Appelli" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" upper stack>
                <p class="text-muted small m-0 mt-1">Supervisione e gestione globale di tutti gli appelli d'esame</p>
            </x-page-header>

            <!-- SCHEDA FILTRI ISTANTANEI -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-end">
                        
                        <!-- Etichetta Filtra -->
                        <div class="col-12 col-md-auto fw-bold text-unibs d-flex align-items-center pb-1">
                            <i class="bi bi-funnel-fill"></i>
                        </div>

                        <!-- 1. Filtro Corso -->
                        <div class="col-12 col-md">
                            <label for="corso_id" class="form-label fw-bold text-dark small mb-1">Corso di Laurea:</label>
                            <select name="corso_id" id="corso_id" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                <option value="">-- Tutti i Corsi --</option>
                                @foreach($corsi as $corso)
                                    <option value="{{ $corso->corso_id }}" 
                                            data-durata="{{ $corso->durata_anni }}"
                                            {{ ($corso_id == $corso->corso_id) ? 'selected' : '' }}>
                                        {{ $corso->nome }} - {{ $corso->tipo_corso }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Filtro Sessione (dipende dal corso scelto) -->
                        <div class="col-12 col-md">
                            <label for="sessione_id" class="form-label fw-bold text-dark small mb-1">Sessione:</label>
                            <select name="sessione_id" id="sessione_id" class="form-select form-select-sm shadow-sm border-unibs select-filtro"
                                    data-testo-vuoto="-- Seleziona prima un Corso --"
                                    data-testo-attivo="-- Tutte le Sessioni --"
                                    {{ $corso_id ? '' : 'disabled' }}>
                                <option value="">-- {{ $corso_id ? 'Tutte le Sessioni' : 'Seleziona prima un Corso' }} --</option>
                                @foreach($sessioni as $sessione)
                                    <option value="{{ $sessione->sessione_id }}" 
                                            data-corso="{{ $sessione->corso_id }}"
                                            {{ ($sessione_id == $sessione->sessione_id) ? 'selected' : '' }}>
                                        {{ $sessione->nome }} ({{ $sessione->anno }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Filtro Anno di Corso (dipende dal corso scelto) -->
                        <div class="col-12 col-md">
                            <label for="anno_di_corso" class="form-label fw-bold text-dark small mb-1">Anno di Corso:</label>
                            <select name="anno_di_corso" 
                                    id="anno_di_corso" 
                                    class="form-select form-select-sm shadow-sm border-unibs select-filtro"
                                    data-initial-value="{{ $anno_di_corso }}"
                                    {{ $corso_id ? '' : 'disabled' }}>
                                <option value="">-- {{ $corso_id ? 'Tutti gli anni' : 'Seleziona prima un Corso' }} --</option>
                            </select>
                        </div>

                        <!-- 4. Pulsante Reset -->
                        <div class="col-12 col-md-auto d-grid">
                            <button type="button" id="btnResetFiltri" class="btn btn-outline-unibs btn-sm fw-bold shadow-sm rounded-2 px-3" title="Reset filtri">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Avviso dinamico nessun risultato -->
            <div id="noAppelliMatchFilter" class="card shadow-sm border-0 rounded-3 mb-4 d-none">
                <div class="card-body text-center text-muted py-5 fs-6">
                    <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                    Nessun appello trovato con i filtri selezionati.
                </div>
            </div>

            <!-- VISUALIZZAZIONE GERARCHICA APPELLI (SESSIONE -> CORSO -> ANNO DEGLI APPELLI) -->
            @if($appelliGrouped->isNotEmpty())
                @foreach($appelliGrouped as $sessionId => $corsiGroup)
                    @php
                        // Recupero dati della sessione
                        $firstAppello = $corsiGroup->first()->first()->first();
                        $sessione = $firstAppello->sessione;
                        $isPassata = $sessione ? \Carbon\Carbon::parse($sessione->data_fine)->lt(\Carbon\Carbon::today()) : false;
                    @endphp

                    <!-- Blocco Sessione d'Esame -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden blocco-sessione" data-sessione-id="{{ $sessionId }}">
                        <div class="card-header bg-unibs text-white py-3 px-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                            <h5 class="m-0 fw-bold fs-6">
                                <i class="bi bi-calendar-range-fill me-2"></i>Sessione: {{ $sessione->nome }} ({{ $sessione->anno }}){{ $sessione->corso ? ' - ' . $sessione->corso->nome . ' (' . $sessione->corso->tipo_corso . ')' : '' }}
                            </h5>
                            <span class="badge bg-white text-unibs fw-bold px-3 py-1.5 rounded-pill align-self-start align-self-md-auto">
                                Periodo: {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="card-body p-0">
                            @foreach($corsiGroup as $corsoNome => $anniGroup)
                                @php
                                    $corsoIdBlocco = $sessione->corso_id ?? $anniGroup->first()->first()->insegnamentoCorso->corso_id ?? '';
                                @endphp

                                <div class="blocco-corso" data-corso-id="{{ $corsoIdBlocco }}">
                                    <!-- Sotto-intestazione Corso di Laurea -->
                                    <div class="bg-light px-4 py-2 border-bottom fw-bold text-dark d-flex align-items-center">
                                        <i class="bi bi-journal-bookmark-fill me-2 text-unibs"></i>Corso: {{ $corsoNome }}
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light border-bottom text-muted small">
                                                <tr>
                                                    <th class="py-2 px-4 text-dark fw-bold">Insegnamento</th>
                                                    <th class="py-2 px-4 text-center text-dark fw-bold">Data e Orario</th>
                                                    <th class="py-2 px-4 text-center text-dark fw-bold">Sede</th>
                                                    <th class="py-2 px-4 text-center text-dark fw-bold">Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($anniGroup as $annoNumero => $appelli)
                                                    <!-- Intestazione Anno di Corso -->
                                                    <tr class="table-secondary border-top border-bottom riga-anno" data-anno-corso="{{ $annoNumero }}">
                                                        <td colspan="4" class="py-1 px-4 fw-bold text-dark small">
                                                            <i class="bi bi-mortarboard-fill me-2 text-unibs"></i>
                                                            @if(is_numeric($annoNumero))
                                                                {{ $annoNumero }}° Anno
                                                            @else
                                                                {{ $annoNumero }}
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <!-- Righe Singoli Appelli -->
                                                    @foreach($appelli as $appello)
                                                        <tr class="riga-appello" data-anno-corso="{{ $annoNumero }}">
                                                            <!-- Insegnamento -->
                                                            <td class="py-3 px-4 fw-semibold text-dark">
                                                                {{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }}
                                                            </td>

                                                            <!-- Data e Orario -->
                                                            <td class="py-3 px-4 text-center text-nowrap">
                                                                <span class="badge border border-unibs text-unibs bg-transparent px-3 py-2 rounded-3 fw-bold d-inline-block text-start">
                                                                    <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($appello->data)->format('d/m/Y') }}
                                                                    <br>
                                                                    <i class="bi bi-clock me-1"></i>{{ substr($appello->ora_inizio, 0, 5) }} - {{ substr($appello->ora_fine, 0, 5) }}
                                                                </span>
                                                            </td>

                                                            <!-- Sede e Aula -->
                                                            <td class="py-3 px-4 text-center text-nowrap">
                                                                @if($appello->aula)
                                                                    <span class="badge border border-unibs text-unibs bg-transparent px-3 py-2 rounded-3 fw-bold d-inline-block text-start">
                                                                        <i class="bi bi-building me-1"></i>{{ $appello->aula->edificio->nome ?? 'N/D' }}
                                                                        <br>
                                                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $appello->aula->nome }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted small">N/D</span>
                                                                @endif
                                                            </td>

                                                            <!-- Azioni (Disabilitate se la sessione è passata) -->
                                                            <td class="py-3 px-4 text-center text-nowrap">
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    @if($isPassata)
                                                                        <x-edit-button title="Non è possibile modificare appelli di sessioni passate" disabled />
                                                                        <x-delete-form title="Non è possibile eliminare appelli di sessioni passate" disabled />
                                                                    @else
                                                                        <x-edit-button :href="route('admin.appelli.edit', $appello->getKey())" title="Modifica Appello" />
                                                                        <x-delete-form :action="route('admin.appelli.destroy', $appello->getKey())" title="Elimina Appello">
                                                                            Sei sicuro di voler eliminare questo appello d'esame?
                                                                        </x-delete-form>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body text-center text-muted py-5 fs-6">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                        Nessun appello trovato nel sistema.
                    </div>
                </div>
            @endif

        </div>

        <!-- Collegamento agli script dei filtri del calendario -->
        <script src="{{ asset('js/sessioniPerCorso.js') }}?v={{ filemtime(public_path('js/sessioniPerCorso.js')) }}"></script>
        <script src="{{ asset('js/filtriCalendario.js') }}?v={{ filemtime(public_path('js/filtriCalendario.js')) }}"></script>

    </body>
</html>