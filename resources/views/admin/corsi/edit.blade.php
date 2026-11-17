<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Modifica Corso di Laurea - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <x-form-header titolo="Modifica Corso di Laurea" :back-href="route('admin.corsi.index')" />

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.corsi.update', $corso->corso_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Nome Corso -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Corso di Laurea <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('nome', $corso->nome) }}" 
                                           required>
                                </div>

                                <!-- Tipologia Corso -->
                                <div class="mb-4">
                                    <label for="tipo_corso" class="form-label fw-bold text-dark small mb-1">
                                        Tipologia Corso <span class="text-danger">*</span>
                                    </label>
                                    <select name="tipo_corso" id="tipo_corso" class="form-select shadow-sm" required>
                                        <option value="Triennale" {{ old('tipo_corso', $corso->tipo_corso) == 'Triennale' ? 'selected' : '' }}>Triennale</option>
                                        <option value="Magistrale" {{ old('tipo_corso', $corso->tipo_corso) == 'Magistrale' ? 'selected' : '' }}>Magistrale</option>
                                        <option value="Magistrale a ciclo unico" {{ old('tipo_corso', $corso->tipo_corso) == 'Magistrale a ciclo unico' ? 'selected' : '' }}>Magistrale a ciclo unico</option>
                                    </select>
                                </div>

                                <x-submit-button icon="pencil-square">Aggiorna Corso</x-submit-button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>