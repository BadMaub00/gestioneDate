<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Gestione Sessioni d'Esame - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="py-3">
            <div class="container-fluid px-md-4 px-lg-5">

                <!-- Intestazione Pagina con Pulsante Torna alla Dashboard -->
                <x-page-header icon="calendar-range-fill" titolo="Gestione Sessioni d'Esame" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" upper stack>
                    <p class="text-muted small m-0 mt-1 fw-medium">
                        Elenco e gestione delle sessioni d'esame registrate nel sistema
                    </p>
                </x-page-header>

                <!-- Alert Messaggi di Successo/Errore -->
                <x-flash-messages />

                <!-- FILTRI OPZIONALI: CORSO E ANNO SESSIONE -->
                @php
                    $anniDisponibili = $sessioni->pluck('anno')->unique()->sort()->values();
                @endphp

                @if($anniDisponibili->count() > 0)
                    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                        <div class="card-body p-3">
                            <div class="row g-2 align-items-end">
                                
                                <!-- Etichetta Filtra -->
                                <div class="col-12 col-md-auto fw-bold text-unibs d-flex align-items-center pb-1">
                                    <i class="bi bi-funnel-fill"></i>
                                </div>

                                <!-- 1. Filtro Corso -->
                                <div class="col-12 col-md">
                                    <label for="filtroCorsoSessione" class="form-label fw-bold text-dark small mb-1">Corso di Laurea:</label>
                                    <select id="filtroCorsoSessione" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                        <option value="" selected>-- Tutti i Corsi --</option>
                                        @foreach($corsi as $corso)
                                            <option value="{{ $corso->corso_id }}">{{ $corso->nome }} - {{ $corso->tipo_corso }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 2. Filtro Anno Sessione -->
                                <div class="col-12 col-md">
                                    <label for="filtroAnnoSessione" class="form-label fw-bold text-dark small mb-1">Anno:</label>
                                    <select id="filtroAnnoSessione" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                        <option value="" selected>-- Tutti gli Anni --</option>
                                        @foreach($anniDisponibili as $anno)
                                            <option value="{{ $anno }}">Anno {{ $anno }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 3. Pulsante Reset -->
                                <div class="col-12 col-md-auto d-grid">
                                    <button type="button" id="btnResetFiltriSessione" class="btn btn-outline-unibs btn-sm fw-bold shadow-sm rounded-2 px-3" title="Reset filtri">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                <!-- Avviso in caso nessun risultato corrisponda al filtro -->
                <div id="noSessioniMatchFilter" class="card shadow-sm border-0 rounded-3 p-4 text-center text-muted d-none mb-4 bg-white">
                    <i class="bi bi-funnel fs-3 text-secondary mb-2"></i>
                    <h6 class="fw-bold m-0">Nessuna sessione trovata per i filtri selezionati.</h6>
                    <small>Imposta "Tutti i corsi" e "Tutti gli anni" per mostrare nuovamente tutte le sessioni d'esame.</small>
                </div>

                <!-- Bottone Aggiungi Sessione -->
                <x-add-button :href="route('admin.sessioni.create')" upper>Aggiungi Sessione</x-add-button>

                <!-- Tabella Sessioni d'Esame -->
                <div id="tabellaSessioniCard" class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="ps-4 py-3 text-unibs" style="width: 20%;">Nome</th>
                                        <th class="py-3 text-unibs" style="width: 25%;">Corso di Studi</th>
                                        <th class="py-3 text-unibs text-center" style="width: 8%;">Anno</th>
                                        <th class="py-3 text-unibs text-center" style="width: 22%;">Periodo</th>
                                        <th class="py-3 text-unibs text-center" style="width: 10%;">N° Appelli</th>
                                        <th class="pe-4 py-3 text-unibs text-end" style="width: 15%;">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sessioni->sortBy([['data_inizio', 'asc'], ['corso_id', 'asc']]) as $sessione)
                                        @php
                                            // Verifica se la sessione si è conclusa prima di oggi
                                            $isPassata = \Carbon\Carbon::parse($sessione->data_fine)->lt(\Carbon\Carbon::today());
                                        @endphp
                                        <tr class="sessione-row" data-anno="{{ $sessione->anno }}" data-corso="{{ $sessione->corso_id }}">
                                            <!-- Nome -->
                                            <td class="ps-4 py-3 fw-bold text-dark">
                                                {{ $sessione->nome }}
                                            </td>

                                            <!-- Corso di Studi -->
                                            <td class="py-3 text-dark">
                                                @if($sessione->corso)
                                                    {{ $sessione->corso->nome }} <span class="text-muted small">- {{ $sessione->corso->tipo_corso }}</span>
                                                @else
                                                    <span class="text-muted">Non assegnato</span>
                                                @endif
                                            </td>

                                            <!-- Anno -->
                                            <td class="py-3 text-center fw-bold text-dark">
                                                {{ $sessione->anno }}
                                            </td>

                                            <!-- Periodo -->
                                            <td class="py-3 text-center text-muted fw-medium">
                                                {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}
                                                <div class="small text-secondary">Scad. inserimento: {{ $sessione->scadenza_effettiva->format('d/m/Y') }}</div>
                                            </td>

                                            <!-- N° Appelli -->
                                            <td class="py-3 text-center fw-bold text-dark">
                                                {{ $sessione->numero_appelli }}
                                            </td>

                                            <!-- Azioni -->
                                            <td class="pe-4 py-3 text-end text-nowrap">
                                                <div class="d-flex justify-content-end gap-1">
                                                    @if($isPassata)
                                                        <!-- Modifica Disabilitata per Sessioni Passate -->
                                                        <x-edit-button title="Non è possibile modificare una sessione passata" disabled small />
                                                    @else
                                                        <!-- Modifica Attiva -->
                                                        <x-edit-button :href="route('admin.sessioni.edit', $sessione->sessione_id)" title="Modifica Sessione" small />
                                                    @endif

                                                    <!-- Elimina con Modal Personalizzato contenente Anno e Nome Sessione -->
                                                    <x-delete-form :action="route('admin.sessioni.destroy', $sessione->sessione_id)" title="Elimina Sessione" small>
                                                        Sei sicuro di voler eliminare la sessione '{{ $sessione->nome }}' dell'anno {{ $sessione->anno }}{{ $sessione->corso ? ' per il corso ' . $sessione->corso->nome . ' (' . $sessione->corso->tipo_corso . ')' : '' }}?
                                                    </x-delete-form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <x-empty-state colspan="6" compact>Nessuna sessione d'esame registrata.</x-empty-state>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Collegamento allo script dei filtri delle sessioni -->
        <script src="{{ asset('js/filtriSessioni.js') }}?v={{ filemtime(public_path('js/filtriSessioni.js')) }}"></script>

        <!-- Script Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>