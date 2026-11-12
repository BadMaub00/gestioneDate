<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Modifica Appello - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-7">

                    <x-page-header icon="pencil-square" titolo="Modifica Appello d'Esame" :back-href="route('admin.calendario', ['sessione_id' => $appello->sessione_id, 'corso_id' => $appello->insegnamentoCorso->corso_id ?? null])" back-label="Torna Indietro" />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <!-- Messaggi di errore globale -->
                            <x-validation-errors :mb="4" />

                            <form action="{{ route('admin.appelli.update', $appello->getKey()) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Insegnamento e Corso (Solo lettura) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Insegnamento / Corso</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="{{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }} ({{ $appello->insegnamentoCorso->corso->nome ?? 'N/D' }})" disabled>
                                </div>

                                <!-- Data dell'Appello (Vincolata al periodo della Sessione) -->
                                <div class="mb-3">
                                    <label for="data" class="form-label fw-bold small text-dark">Data Esame (Lun - Ven) <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           name="data" 
                                           id="data" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('data', $appello->data) }}" 
                                           min="{{ $appello->sessione->data_inizio ?? null }}" 
                                           max="{{ $appello->sessione->data_fine ?? null }}" 
                                           required>
                                </div>

                                <!-- Orari Inizio e Fine (08:00 - 19:00) -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label for="ora_inizio" class="form-label fw-bold small text-dark">Ora Inizio (dalle 08:00) <span class="text-danger">*</span></label>
                                        <input type="time" 
                                               name="ora_inizio" 
                                               id="ora_inizio" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('ora_inizio', \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i')) }}" 
                                               min="08:00" 
                                               max="18:00" 
                                               required>
                                    </div>
                                    <div class="col-6">
                                        <label for="ora_fine" class="form-label fw-bold small text-dark">Ora Fine (entro le 19:00) <span class="text-danger">*</span></label>
                                        <input type="time" 
                                               name="ora_fine" 
                                               id="ora_fine" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('ora_fine', \Carbon\Carbon::parse($appello->ora_fine)->format('H:i')) }}" 
                                               min="09:00" 
                                               max="19:00" 
                                               required>
                                    </div>
                                </div>

                                <!-- Selezione Edificio -->
                                <div class="mb-3">
                                    <label for="edificio_id" class="form-label fw-bold small text-dark">Edificio <span class="text-danger">*</span></label>
                                    <select name="edificio_id" id="edificio_id" class="form-select shadow-sm" required>
                                        <option value="" disabled>-- Seleziona Edificio --</option>
                                        @foreach($edifici as $edificio)
                                            <option value="{{ $edificio->edificio_id }}" 
                                                {{ old('edificio_id', $appello->aula->edificio_id ?? null) == $edificio->edificio_id ? 'selected' : '' }}>
                                                {{ $edificio->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Selezione Aula -->
                                <div class="mb-3">
                                    <label for="aula_id" class="form-label fw-bold small text-dark">Aula <span class="text-danger">*</span></label>
                                    <select name="aula_id" id="aula_id" class="form-select shadow-sm" required>
                                        <option value="" disabled>-- Seleziona prima un Edificio --</option>
                                        @foreach($aule as $aula)
                                            <option value="{{ $aula->aula_id }}" 
                                                    data-edificio-id="{{ $aula->edificio_id }}"
                                                    {{ old('aula_id', $appello->aula_id) == $aula->aula_id ? 'selected' : '' }}>
                                                {{ $aula->nome }} (Capienza: {{ $aula->capienza ?? 'N/D' }} posti)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Note / Informazioni Aggiuntive -->
                                <div class="mb-4">
                                    <label for="note" class="form-label fw-bold small text-dark">Note / Informazioni Aggiuntive (opzionale)</label>
                                    <textarea name="note" 
                                              id="note" 
                                              class="form-control shadow-sm @error('note') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="Es. Modalità d'esame, uso calcolatrice, suddivisione turni...">{{ old('note', $appello->note ?? '') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Salva Modifiche
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Collegamento allo script esterno -->
        <script src="{{ asset('js/editAppelloAdmin.js') }}"></script>

    </body>
</html>