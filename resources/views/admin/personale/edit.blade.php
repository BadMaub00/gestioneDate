<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Modifica Personale - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Modifica Utente</h1>
                        <a href="{{ route('admin.personale.index') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.personale.update', $utente->utente_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-bold text-dark small mb-1">
                                        Username <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="username" 
                                           id="username" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('username', $utente->username) }}" 
                                           required>
                                </div>

                                <!-- Nome e Cognome -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                            Nome <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               name="nome" 
                                               id="nome" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('nome', $utente->nome) }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cognome" class="form-label fw-bold text-dark small mb-1">
                                            Cognome <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               name="cognome" 
                                               id="cognome" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('cognome', $utente->cognome) }}" 
                                               required>
                                    </div>
                                </div>

                                <!-- Data di Nascita e Ruolo -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_nascita" class="form-label fw-bold text-dark small mb-1">
                                            Data di Nascita <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               name="data_nascita" 
                                               id="data_nascita" 
                                               class="form-control shadow-sm" 
                                               value="{{ old('data_nascita', $utente->data_nascita) }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ruolo" class="form-label fw-bold text-dark small mb-1">
                                            Ruolo <span class="text-danger">*</span>
                                        </label>
                                        <select name="ruolo" id="ruolo" class="form-select shadow-sm" required>
                                            <option value="Docente" {{ old('ruolo', $utente->ruolo) == 'Docente' ? 'selected' : '' }}>Docente</option>
                                            <option value="Amministratore" {{ old('ruolo', $utente->ruolo) == 'Amministratore' ? 'selected' : '' }}>Amministratore</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Modifica Password (Opzionale) -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold text-dark small mb-1">
                                        Nuova Password <span class="text-muted fw-normal">(opzionale)</span>
                                    </label>
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="form-control shadow-sm" 
                                           placeholder="Lascia vuoto per non cambiarla">
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Aggiorna Utente
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>