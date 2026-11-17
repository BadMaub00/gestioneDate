<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Personale Universitario - Università degli Studi di Brescia" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale con Larghezza Ridotta -->
        <div class="container py-3" style="max-width: 900px;">
            
            <!-- Messaggi di Feedback -->
            <x-flash-messages />

            <!-- Titolo, Sottotitolo e Pulsante Torna alla Dashboard -->
            <x-page-header icon="people-fill" titolo="Personale Universitario" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" upper>
                <p class="text-muted small mb-0 mt-1">Elenco e gestione degli utenti registrati nel sistema</p>
            </x-page-header>

            <!-- SCHEDA FILTRI ISTANTANEI -->
            @if($personale->isNotEmpty())
                <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-end">

                            <!-- Etichetta Filtra -->
                            <div class="col-12 col-md-auto fw-bold text-unibs d-flex align-items-center pb-1">
                                <i class="bi bi-funnel-fill"></i>
                            </div>

                            <!-- 1. Filtro Ruolo -->
                            <div class="col-12 col-md">
                                <label for="filtro_ruolo" class="form-label fw-bold text-dark small mb-1">Ruolo:</label>
                                <select id="filtro_ruolo" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                    <option value="">-- Tutti i Ruoli --</option>
                                    @foreach($personale->pluck('ruolo')->unique()->sort() as $ruolo)
                                        <option value="{{ $ruolo }}">{{ $ruolo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. Filtro Cognome -->
                            <div class="col-12 col-md">
                                <label for="filtro_cognome" class="form-label fw-bold text-dark small mb-1">Cognome:</label>
                                <select id="filtro_cognome" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                    <option value="">-- Tutti i Cognomi --</option>
                                    @foreach($personale->pluck('cognome')->unique()->sort() as $cognome)
                                        <option value="{{ $cognome }}">{{ $cognome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 3. Filtro Nome -->
                            <div class="col-12 col-md">
                                <label for="filtro_nome" class="form-label fw-bold text-dark small mb-1">Nome:</label>
                                <select id="filtro_nome" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                    <option value="">-- Tutti i Nomi --</option>
                                    @foreach($personale->pluck('nome')->unique()->sort() as $nome)
                                        <option value="{{ $nome }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 4. Pulsante Reset -->
                            <div class="col-12 col-md-auto d-grid">
                                <button type="button" id="btnResetFiltriPersonale" class="btn btn-outline-unibs btn-sm fw-bold shadow-sm rounded-2 px-3" title="Reset filtri">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Avviso dinamico nessun risultato -->
                <div id="noPersonaleMatchFilter" class="card shadow-sm border-0 rounded-3 mb-4 d-none bg-white">
                    <div class="card-body text-center text-muted py-5 fs-6">
                        <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                        Nessun utente trovato con i filtri selezionati.
                    </div>
                </div>
            @endif

            <!-- Blocco Strutturale Aggiungi Personale -->
            <x-add-button :href="route('admin.personale.create')" icon="person-plus-fill" upper large>Aggiungi Personale</x-add-button>

            <!-- Tabella Personale (Omologata) -->
            <div id="tabellaPersonaleCard" class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Cognome</th>
                                <th class="py-3 px-4 text-dark fw-bold">Nome</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Ruolo</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personale as $p)
                                <tr class="riga-personale" data-ruolo="{{ $p->ruolo }}" data-cognome="{{ $p->cognome }}" data-nome="{{ $p->nome }}">
                                    <td class="py-3 px-4 fw-semibold text-dark">{{ $p->cognome }}</td>
                                    <td class="py-3 px-4 fw-semibold text-dark">{{ $p->nome }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge border border-unibs text-unibs bg-transparent rounded-pill py-2 fw-bold d-inline-block" style="width: 140px;">
                                            {{ $p->ruolo }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Pulsante Circolare Modifica -->
                                            <x-edit-button :href="route('admin.personale.edit', $p->utente_id)" title="Modifica Utente" />

                                            <!-- Pulsante Circolare Elimina con Modal Personalizzato -->
                                            <x-delete-form :action="route('admin.personale.destroy', $p->utente_id)" title="Elimina Utente">
                                                Sei sicuro di voler eliminare l'utente '{{ $p->nome }} {{ $p->cognome }}'?
                                            </x-delete-form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <x-empty-state colspan="4" icon="inbox">Nessun utente presente nel sistema.</x-empty-state>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Script Filtri Dinamici e Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/filtriPersonale.js') }}?v={{ filemtime(public_path('js/filtriPersonale.js')) }}"></script>
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>