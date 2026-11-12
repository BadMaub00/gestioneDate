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

            <!-- SCHEDA FILTRI FACOLTATIVI -->
            <div class="card shadow-sm border-0 rounded-3 mb-4 bg-white">
                <div class="card-body p-3">
                    <form action="{{ route('admin.personale.associazioni_index') }}" method="GET" class="row g-3 align-items-end">
                        
                        <!-- 1. Filtro Corso -->
                        <div class="col-12 col-md-3">
                            <label for="filtro_corso_id" class="form-label fw-bold text-dark small mb-1">Corso di Laurea:</label>
                            <select name="corso_id" id="filtro_corso_id" class="form-select form-select-sm shadow-sm">
                                <option value="">-- Tutti i corsi --</option>
                                @foreach($corsi as $corso)
                                    <option value="{{ $corso->corso_id }}" {{ request('corso_id') == $corso->corso_id ? 'selected' : '' }}>
                                        {{ $corso->nome }} ({{ $corso->tipo_corso }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Filtro Insegnamento -->
                        <div class="col-12 col-md-3">
                            <label for="filtro_ins_corso_id" class="form-label fw-bold text-dark small mb-1">Insegnamento:</label>
                            <select name="ins_corso_id" 
                                    id="filtro_ins_corso_id" 
                                    class="form-select form-select-sm shadow-sm"
                                    data-initial-value="{{ request('ins_corso_id') }}"
                                    {{ request('corso_id') ? '' : 'disabled' }}>
                                <option value="">-- {{ request('corso_id') ? 'Tutti gli insegnamenti del corso' : 'Seleziona prima un Corso' }} --</option>
                                @foreach($insegnamentiCorso as $insCorso)
                                    <option value="{{ $insCorso->ins_corso_id }}" 
                                            data-corso-id="{{ $insCorso->corso_id }}"
                                            {{ request('ins_corso_id') == $insCorso->ins_corso_id ? 'selected' : '' }}>
                                        {{ $insCorso->insegnamento->nome ?? 'N/D' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Filtro Docente -->
                        <div class="col-12 col-md-3">
                            <label for="filtro_docente_id" class="form-label fw-bold text-dark small mb-1">Docente:</label>
                            <select name="docente_id" id="filtro_docente_id" class="form-select form-select-sm shadow-sm">
                                <option value="">-- Tutti i docenti --</option>
                                @foreach($docenti as $docente)
                                    <option value="{{ $docente->utente_id }}" {{ request('docente_id') == $docente->utente_id ? 'selected' : '' }}>
                                        {{ $docente->cognome }} {{ $docente->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Pulsanti Filtra / Resetta -->
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-unibs btn-sm text-white fw-bold w-100 shadow-sm rounded-2">
                                <i class="bi bi-funnel-fill me-1"></i> Filtra
                            </button>
                            @if(request()->hasAny(['corso_id', 'ins_corso_id', 'docente_id']))
                                <a href="{{ route('admin.personale.associazioni_index') }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm rounded-2">
                                    <i class="bi bi-x-circle me-1"></i> Reset
                                </a>
                            @endif
                        </div>

                    </form>
                </div>
            </div>

            <!-- Pulsante Aggiungi Associazione -->
            <div class="mb-4">
                <a href="{{ route('admin.personale.associa') }}" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i>Aggiungi Associazione
                </a>
            </div>

            <!-- Tabella Associazioni Raggruppate per Corso -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
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
                                    <!-- Intestazione Gruppo Corso -->
                                    <tr class="table-light">
                                        <td colspan="5" class="py-2 px-4 fw-bold text-unibs border-top border-bottom">
                                            <i class="bi bi-journal-bookmark-fill me-2"></i>{{ $corsoNome }}
                                        </td>
                                    </tr>

                                    <!-- Righe Associazioni del Corso -->
                                    @foreach($associazioni as $assoc)
                                        <tr>
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
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                        Nessuna associazione trovata con i filtri selezionati.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Script Filtri Dinamici e Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/filtriAssociazioni.js') }}"></script>
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>