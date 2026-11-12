<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Nuovo Edificio - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Aggiungi Nuovo Edificio</h1>
                        <a href="{{ route('admin.sedi.edifici') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.sedi.storeEdificio') }}" method="POST">
                                @csrf

                                <!-- Nome Edificio -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Edificio / Polo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           placeholder="es. Polo Didattico Brixia" 
                                           value="{{ old('nome') }}" 
                                           required>
                                </div>

                                <!-- Indirizzo / Via -->
                                <div class="mb-4">
                                    <label for="via" class="form-label fw-bold text-dark small mb-1">
                                        Via / Indirizzo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="via" 
                                           id="via" 
                                           class="form-control shadow-sm" 
                                           placeholder="es. Via Branze 38, Brescia" 
                                           value="{{ old('via') }}" 
                                           required>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i>Salva Edificio
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>