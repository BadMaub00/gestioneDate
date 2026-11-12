<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Modifica Aula - ' . $aula->nome" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h1 class="h4 fw-bold text-unibs m-0">Modifica Aula</h1>
                            <p class="text-muted small m-0">Edificio: <strong>{{ $aula->edificio->nome ?? 'N/D' }}</strong></p>
                        </div>
                        <a href="{{ route('admin.sedi.aule', $aula->edificio_id) }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.sedi.updateAula', $aula->aula_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Nome Aula -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Aula <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('nome', $aula->nome) }}" 
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
                                           min="1"
                                           value="{{ old('capienza', $aula->capienza) }}" 
                                           required>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Aggiorna Aula
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>