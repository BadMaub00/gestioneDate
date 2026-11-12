<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Nuovo Personale Universitario - Università degli Studi di Brescia" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            
            <!-- Header della Pagina con Pulsante Pill Torna all'Elenco -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-person-plus-fill me-2"></i>Aggiungi Personale
                    </h1>
                    <p class="text-muted small mb-0 mt-1">Inserisci un nuovo docente o amministratore nel sistema</p>
                </div>
                <a href="{{ route('admin.personale.index') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase">
                    <i class="bi bi-arrow-left me-2"></i>TORNA ALL'ELENCO
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <!-- Gestione Errori Validazione -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Attenzione:</div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Card Form -->
                    <div class="card shadow-sm border-0 rounded-3 bg-white">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.personale.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="nome" class="form-label text-dark fw-bold">Nome <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control rounded-3" required value="{{ old('nome') }}" placeholder="Es. Mario">
                                </div>

                                <div class="mb-3">
                                    <label for="cognome" class="form-label text-dark fw-bold">Cognome <span class="text-danger">*</span></label>
                                    <input type="text" name="cognome" id="cognome" class="form-control rounded-3" required value="{{ old('cognome') }}" placeholder="Es. Rossi">
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label text-dark fw-bold">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" class="form-control rounded-3" required value="{{ old('username') }}" placeholder="Es. mrossi">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label text-dark fw-bold">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control rounded-3" required minlength="6" placeholder="Minimo 6 caratteri">
                                </div>

                                <div class="mb-3">
                                    <label for="data_nascita" class="form-label text-dark fw-bold">Data di Nascita <span class="text-danger">*</span></label>
                                    <input type="date" name="data_nascita" id="data_nascita" class="form-control rounded-3" required value="{{ old('data_nascita') }}">
                                </div>

                                <div class="mb-4">
                                    <label for="ruolo" class="form-label text-dark fw-bold">Ruolo <span class="text-danger">*</span></label>
                                    <select name="ruolo" id="ruolo" class="form-select rounded-3" required>
                                        <option value="Docente" {{ old('ruolo') == 'Docente' ? 'selected' : '' }}>Docente</option>
                                        <option value="Amministratore" {{ old('ruolo') == 'Amministratore' ? 'selected' : '' }}>Amministratore</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-3 py-2.5 shadow-sm d-inline-flex align-items-center justify-content-center text-uppercase">
                                    <i class="bi bi-person-plus-fill me-2"></i>CREA UTENTE
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>