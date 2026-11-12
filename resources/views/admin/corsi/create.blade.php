<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Nuovo Corso di Laurea - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">

                    <x-page-header icon="mortarboard-fill" titolo="Aggiungi Corso di Laurea" :back-href="route('admin.corsi.index')" back-label="Torna ai Corsi" />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <form action="{{ route('admin.corsi.store') }}" method="POST">
                                @csrf

                                <!-- Nome Corso -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome Corso di Laurea <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" placeholder="es. Ingegneria Informatica" required>
                                </div>

                                <!-- Tipologia Corso -->
                                <div class="mb-4">
                                    <label for="tipo_corso" class="form-label fw-bold">Tipologia Corso <span class="text-danger">*</span></label>
                                    <select name="tipo_corso" id="tipo_corso" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona Tipologia --</option>
                                        <option value="Triennale">Triennale</option>
                                        <option value="Magistrale">Magistrale</option>
                                        <option value="Magistrale a ciclo unico">Magistrale a ciclo unico</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Salva Corso
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