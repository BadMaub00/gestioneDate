<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Associazioni Docente-Insegnamento - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">

            <x-flash-messages />

            <!-- Intestazione -->
            <x-page-header icon="people-fill" titolo="Associazioni Docenti - Insegnamenti" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard">
                <p class="text-muted small m-0 mt-1">Elenco e gestione dei docenti assegnati agli insegnamenti dei vari corsi</p>
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
                            <label for="filtro_corso_id" class="form-label fw-bold text-dark small mb-1">Corso di Laurea:</label>
                            <select id="filtro_corso_id" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                <option value="">-- Tutti i Corsi --</option>
                                @foreach($corsi as $corso)
                                    <option value="{{ $corso->corso_id }}">
                                        {{ $corso->nome }} - {{ $corso->tipo_corso }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Filtro Insegnamento (dipende dal corso scelto) -->
                        <div class="col-12 col-md">
                            <label for="filtro_ins_corso_id" class="form-label fw-bold text-dark small mb-1">Insegnamento:</label>
                            <select id="filtro_ins_corso_id" 
                                    class="form-select form-select-sm shadow-sm border-unibs select-filtro"
                                    disabled>
                                <option value="">-- Seleziona prima un Corso --</option>
                                @foreach($insegnamentiCorso as $insCorso)
                                    <option value="{{ $insCorso->ins_corso_id }}" 
                                            data-corso-id="{{ $insCorso->corso_id }}">
                                        {{ $insCorso->insegnamento->nome ?? 'N/D' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Filtro Docente -->
                        <div class="col-12 col-md">
                            <label for="filtro_docente_id" class="form-label fw-bold text-dark small mb-1">Docente:</label>
                            <select id="filtro_docente_id" class="form-select form-select-sm shadow-sm border-unibs select-filtro">
                                <option value="">-- Tutti i Docenti --</option>
                                @foreach($docenti as $docente)
                                    <option value="{{ $docente->utente_id }}">
                                        {{ $docente->cognome }} {{ $docente->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Pulsante Reset -->
                        <div class="col-12 col-md-auto d-grid">
                            <button type="button" id="btnResetFiltriAssociazioni" class="btn btn-outline-unibs btn-sm fw-bold shadow-sm rounded-2 px-3" title="Reset filtri">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Avviso dinamico nessun risultato -->
            <div id="noAssociazioniMatchFilter" class="card shadow-sm border-0 rounded-3 mb-4 d-none bg-white">
                <div class="card-body text-center text-muted py-5 fs-6">
                    <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                    Nessuna associazione trovata con i filtri selezionati.
                </div>
            </div>

            <!-- Pulsante Aggiungi Associazione -->
            <x-add-button :href="route('admin.personale.associa')" icon="person-plus-fill">Aggiungi Associazione</x-add-button>

            <!-- Tabella Associazioni Raggruppate per Corso -->
            <div id="tabellaAssociazioniCard" class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Docente</th>
                                <th class="py-3 px-4 text-dark fw-bold">Insegnamento</th>
                                <th class="py-3 px-4 text-dark fw-bold">Corso di Laurea</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Ruolo</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($associazioniGrouped->isNotEmpty())
                                @foreach($associazioniGrouped as $corsoNome => $associazioni)
                                    @php
                                        $corsoIdGruppo = $associazioni->first()->insegnamentiCorso->corso_id ?? '';
                                    @endphp
                                    <!-- Intestazione Gruppo Corso -->
                                    <tr class="table-light gruppo-corso-header" data-corso-id="{{ $corsoIdGruppo }}">
                                        <td colspan="5" class="py-2 px-4 fw-bold text-unibs border-top border-bottom">
                                            <i class="bi bi-journal-bookmark-fill me-2"></i>{{ $corsoNome }}
                                        </td>
                                    </tr>

                                    <!-- Righe Associazioni del Corso -->
                                    @foreach($associazioni as $assoc)
                                        <tr class="riga-associazione"
                                            data-corso-id="{{ $assoc->insegnamentiCorso->corso_id ?? '' }}"
                                            data-ins-corso-id="{{ $assoc->ins_corso_id }}"
                                            data-docente-id="{{ $assoc->utente_id }}">
                                            <!-- Docente (Cognome e Nome) -->
                                            <td class="py-3 px-4 fw-bold text-dark">
                                                {{ $assoc->docente->cognome ?? '' }} {{ $assoc->docente->nome ?? 'N/D' }}
                                            </td>

                                            <!-- Insegnamento -->
                                            <td class="py-3 px-4 fw-semibold text-dark">
                                                {{ $assoc->insegnamentiCorso->insegnamento->nome ?? 'N/D' }}
                                            </td>

                                            <!-- Corso di Laurea -->
                                            <td class="py-3 px-4 text-dark">
                                                {{ $assoc->insegnamentiCorso->corso->nome ?? 'N/D' }}
                                            </td>

                                            <!-- Ruolo -->
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge border border-unibs text-unibs bg-transparent rounded-pill py-2 fw-bold d-inline-block" style="width: 135px;">
                                                    {{ $assoc->ruolo }}
                                                </span>
                                            </td>

                                            <!-- Azioni -->
                                            <td class="py-3 px-4 text-center text-nowrap">
                                                <div class="d-flex justify-content-center gap-2">

                                                    <!-- Tasto Elimina con Modal Personalizzato -->
                                                    <x-delete-form :action="route('admin.personale.destroyAssocia', $assoc->getKey())" title="Rimuovi Associazione">
                                                        Sei sicuro di voler rimuovere l'associazione del docente '{{ $assoc->docente->cognome ?? '' }} {{ $assoc->docente->nome ?? '' }}' dall'insegnamento '{{ $assoc->insegnamentiCorso->insegnamento->nome ?? 'selezionato' }}'?
                                                    </x-delete-form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @else
                                <x-empty-state colspan="5" icon="people">Nessuna associazione trovata con i filtri selezionati.</x-empty-state>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Script Filtri Dinamici e Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/filtriAssociazioni.js') }}?v={{ filemtime(public_path('js/filtriAssociazioni.js')) }}"></script>
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>