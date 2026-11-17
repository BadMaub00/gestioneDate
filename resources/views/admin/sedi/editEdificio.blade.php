<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Modifica Edificio - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <x-form-header titolo="Modifica Edificio" :back-href="route('admin.sedi.edifici')" />

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.sedi.updateEdificio', $edificio->edificio_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Nome Edificio -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Edificio / Polo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('nome', $edificio->nome) }}" 
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
                                           value="{{ old('via', $edificio->via ?? $edificio->indirizzo) }}" 
                                           required>
                                </div>

                                <x-submit-button icon="pencil-square">Aggiorna Edificio</x-submit-button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>