<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Modifica Appello - ' . ($appello->insegnamentoCorso->insegnamento->nome ?? 'Insegnamento')" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <script src="{{ asset('js/editAppelloDocente.js') }}"></script>

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i>Modifica Appello d'Esame
                    </h1>
                    <p class="text-muted small m-0 mt-1">Aggiorna le informazioni relative all'appello d'esame selezionato</p>
                </div>

                <a href="{{ route('docente.appelliInsegnamento', $appello->ins_corso_id) }}" 
                   class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase">
                    <i class="bi bi-arrow-left me-2"></i>TORNA AGLI APPELLI
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Messaggi di Errore -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Attenzione:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-unibs text-white py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="bi bi-pencil-fill me-2"></i>Dettagli Appello
                    </h5>
                </div>

                <div class="card-body p-4 bg-white">
                    <form action="{{ route('docente.appelli.update', $appello->getKey()) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <!-- Banner Informativo Insegnamento & Sessione Preimpostati -->
                            <div class="col-12 mb-2">
                                <div class="row g-3">
                                    
                                    <!-- Insegnamento -->
                                    <div class="col-12 col-md-6">
                                        <div class="card bg-light border-unibs border-start border-4 rounded-3 p-3 h-100">
                                            <span class="badge bg-unibs text-white px-2.5 py-1 mb-1 align-self-start">Insegnamento</span>
                                            <h6 class="fw-bold text-dark m-0 fs-6">
                                                <i class="bi bi-book-fill me-2 text-unibs"></i>{{ $appello->insegnamentoCorso->insegnamento->nome ?? 'N/D' }}
                                            </h6>
                                            <small class="text-muted mt-1">
                                                Corso: <strong>{{ $appello->insegnamentoCorso->corso->nome ?? 'N/D' }}</strong> ({{ $appello->insegnamentoCorso->anno_di_corso ?? 'N/D' }}° Anno)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Sessione -->
                                    <div class="col-12 col-md-6">
                                        <div class="card bg-light border-unibs border-start border-4 rounded-3 p-3 h-100">
                                            <span class="badge bg-unibs text-white px-2.5 py-1 mb-1 align-self-start">Sessione d'Esame</span>
                                            <h6 class="fw-bold text-dark m-0 fs-6">
                                                <i class="bi bi-calendar-range-fill me-2 text-unibs"></i>{{ $sessione->nome ?? $appello->sessione->nome }} ({{ $sessione->anno ?? $appello->sessione->anno }})
                                            </h6>
                                            <small class="text-muted mt-1">
                                                Periodo: 
                                                <strong>{{ \Carbon\Carbon::parse($sessione->data_inizio ?? $appello->sessione->data_inizio)->format('d/m/Y') }}</strong> - 
                                                <strong>{{ \Carbon\Carbon::parse($sessione->data_fine ?? $appello->sessione->data_fine)->format('d/m/Y') }}</strong><br>
                                                Scadenza inserimento appelli: <strong>{{ (isset($sessione) ? $sessione : $appello->sessione)->scadenza_effettiva->format('d/m/Y') }}</strong>
                                            </small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- 1. Data dell'Appello -->
                            <div class="col-12 col-md-6">
                                <label for="data" class="form-label fw-bold text-dark small mb-1">
                                    Data Appello (Lun - Ven) <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="data" 
                                       id="data" 
                                       class="form-control shadow-sm" 
                                       value="{{ old('data', $appello->data) }}" 
                                       min="{{ $sessione->data_inizio ?? $appello->sessione->data_inizio }}" 
                                       max="{{ $sessione->data_fine ?? $appello->sessione->data_fine }}" 
                                       required>
                            </div>

                            <!-- 2. Orario Inizio -->
                            <div class="col-12 col-md-3">
                                <label for="ora_inizio" class="form-label fw-bold text-dark small mb-1">
                                    Orario Inizio (dalle 08:00) <span class="text-danger">*</span>
                                </label>
                                <input type="time" 
                                       name="ora_inizio" 
                                       id="ora_inizio" 
                                       class="form-control shadow-sm" 
                                       value="{{ old('ora_inizio', \Carbon\Carbon::parse($appello->ora_inizio)->format('H:i')) }}" 
                                       min="08:00" 
                                       max="18:00" 
                                       required>
                            </div>

                            <!-- 3. Orario Fine -->
                            <div class="col-12 col-md-3">
                                <label for="ora_fine" class="form-label fw-bold text-dark small mb-1">
                                    Orario Fine (entro le 19:00) <span class="text-danger">*</span>
                                </label>
                                <input type="time" 
                                       name="ora_fine" 
                                       id="ora_fine" 
                                       class="form-control shadow-sm" 
                                       value="{{ old('ora_fine', \Carbon\Carbon::parse($appello->ora_fine)->format('H:i')) }}" 
                                       min="09:00" 
                                       max="19:00" 
                                       required>
                            </div>

                            <!-- 4. Selezione Edificio -->
                            <div class="col-12 col-md-6">
                                <label for="edificio_id" class="form-label fw-bold text-dark small mb-1">
                                    Edificio / Polo Didattico <span class="text-danger">*</span>
                                </label>
                                <select id="edificio_id" class="form-select shadow-sm" required>
                                    <option value="" disabled>-- Seleziona Edificio --</option>
                                    @foreach($edifici as $edificio)
                                        <option value="{{ $edificio->edificio_id }}" 
                                            {{ old('edificio_id', $appello->aula->edificio_id ?? null) == $edificio->edificio_id ? 'selected' : '' }}>
                                            {{ $edificio->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 5. Selezione Aula (Filtrata dinamicamente via JS) -->
                            <div class="col-12 col-md-6">
                                <label for="aula_id" class="form-label fw-bold text-dark small mb-1">
                                    Aula d'Esame <span class="text-danger">*</span>
                                </label>
                                <select name="aula_id" id="aula_id" class="form-select shadow-sm" required>
                                    <option value="" disabled>-- Seleziona prima un Edificio --</option>
                                    @foreach($edifici as $edificio)
                                        @foreach($edificio->aule as $aula)
                                            <option value="{{ $aula->aula_id }}" 
                                                    data-edificio-id="{{ $edificio->edificio_id }}"
                                                    {{ old('aula_id', $appello->aula_id) == $aula->aula_id ? 'selected' : '' }}>
                                                {{ $aula->nome }} (Capienza: {{ $aula->capienza ?? 'N/D' }} posti)
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <!-- 6. Note / Informazioni Aggiuntive -->
                            <div class="col-12">
                                <label for="note" class="form-label fw-bold text-dark small mb-1">
                                    Note / Informazioni Aggiuntive <span class="text-muted fw-normal">(opzionale)</span>
                                </label>
                                <textarea name="note" 
                                          id="note" 
                                          class="form-control shadow-sm @error('note') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Es. Modalità d'esame, uso calcolatrice, suddivisione turni, materiale consentito...">{{ old('note', $appello->note ?? '') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- Bottoni d'Azione -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('docente.appelliInsegnamento', $appello->ins_corso_id) }}" class="btn btn-outline-secondary fw-bold px-4 rounded-3">
                                Annulla
                            </a>
                            <button type="submit" class="btn btn-unibs text-white fw-bold px-4 rounded-3 shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Salva Modifiche
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

    </body>
</html>