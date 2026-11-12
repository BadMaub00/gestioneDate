<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Nuova Sessione d'Esame" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />
        
        <x-navbar />

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Crea Sessione d'Esame</h1>
                        <a href="{{ route('admin.sessioni.index') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.sessioni.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome Sessione <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" placeholder="es. Sessione Invernale" required value="{{ old('nome') }}">
                                </div>

                                @include('admin.sessioni._corsi', ['corsi' => $corsi, 'selezionati' => []])

                                <div class="mb-3">
                                    <label for="numero_appelli" class="form-label fw-bold">N° Appelli Previsti (1-4) <span class="text-danger">*</span></label>
                                    <input type="number" name="numero_appelli" id="numero_appelli" class="form-control" min="1" max="4" required value="{{ old('numero_appelli', 2) }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_inizio" class="form-label fw-bold">Data Inizio <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               name="data_inizio" 
                                               id="data_inizio" 
                                               class="form-control" 
                                               min="{{ \Carbon\Carbon::today()->addMonth()->format('Y-m-d') }}" 
                                               required 
                                               value="{{ old('data_inizio') }}">
                                        <div class="form-text small">Deve distare almeno 1 mese da oggi.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="data_fine" class="form-label fw-bold">Data Fine <span class="text-danger">*</span></label>
                                        <input type="date" name="data_fine" id="data_fine" class="form-control" required value="{{ old('data_fine') }}">
                                        <div class="form-text small">Almeno 2 settimane dopo l'inizio.</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="scadenza_inserimento" class="form-label fw-bold">Scadenza inserimento appelli</label>
                                    <input type="date" name="scadenza_inserimento" id="scadenza_inserimento" class="form-control" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ old('scadenza_inserimento') }}">
                                    <div class="form-text small">Facoltativa. Ultimo giorno in cui i docenti possono inserire gli appelli; se vuota viene impostata a un mese prima dell'inizio della sessione.</div>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 mt-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i>Salva Sessione
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collegamento allo script esterno -->
        <script src="{{ asset('js/createSessione.js') }}"></script>
        <script src="{{ asset('js/selezioneCorsiSessione.js') }}"></script>
    </body>
</html>