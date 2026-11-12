<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Nuovo Insegnamento - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">

                    <x-page-header icon="journal-plus" titolo="Aggiungi Nuovo Insegnamento" :back-href="route('admin.insegnamenti.index')" back-label="Torna agli Insegnamenti" />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <form action="{{ route('admin.insegnamenti.store') }}" method="POST">
                                @csrf

                                <!-- Nome Insegnamento -->
                                <div class="mb-4">
                                    <label for="nome" class="form-label fw-bold">Nome Insegnamento <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" placeholder="es. Programmazione Web" required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Salva Insegnamento
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>