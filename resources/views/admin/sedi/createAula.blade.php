<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Nuova Aula - ' . $edificio->nome" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h1 class="h4 fw-bold text-unibs m-0">Aggiungi Nuova Aula</h1>
                            <p class="text-muted small m-0">Edificio: <strong>{{ $edificio->nome }}</strong></p>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                        <a href="{{ route('admin.sedi.aule', $edificio->edificio_id) }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                </div>
                    </div>

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.sedi.storeAula', $edificio->edificio_id) }}" method="POST">
                                @csrf

                                <!-- Nome Aula -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Aula <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           placeholder="es. Aula B1.2" 
                                           value="{{ old('nome') }}" 
                                           required>
                                </div>

                                <!-- Capienza -->
                                <div class="mb-4">
                                    <label for="capienza" class="form-label fw-bold text-dark small mb-1">
                                        Capienza (Posti a sedere) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           name="capienza" 
                                           id="capienza" 
                                           class="form-control shadow-sm" 
                                           placeholder="es. 120" 
                                           min="1"
                                           value="{{ old('capienza') }}" 
                                           required>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i>Salva Aula
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>