<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Modifica Insegnamento - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <x-form-header titolo="Modifica Insegnamento" :back-href="route('admin.insegnamenti.index')" />

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.insegnamenti.update', $insegnamento->insegnamento_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Nome Insegnamento -->
                                <div class="mb-4">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Insegnamento / Materia <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           value="{{ old('nome', $insegnamento->nome) }}" 
                                           required>
                                </div>

                                <x-submit-button icon="pencil-square">Aggiorna Insegnamento</x-submit-button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>