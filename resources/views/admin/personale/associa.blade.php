<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Associa Docente a Insegnamento" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8">

                    <x-page-header icon="person-plus-fill" titolo="Associa Docente a Insegnamento" :back-href="route('admin.personale.associazioni_index')" back-label="Torna Indietro" />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <form action="{{ route('admin.personale.storeAssocia') }}" method="POST">
                                @csrf

                                <!-- 1. Seleziona Corso -->
                                <div class="mb-3">
                                    <label for="corso_id" class="form-label fw-bold">Corso di Studio <span class="text-danger">*</span></label>
                                    <select name="corso_id" id="corso_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona un corso --</option>
                                        @foreach($corsi as $c)
                                            <option value="{{ $c->corso_id }}">
                                                {{ $c->nome }} ({{ $c->tipo_corso }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 2. Seleziona Insegnamento (Disabilitato inizialmente) -->
                                <div class="mb-3">
                                    <label for="ins_corso_id" class="form-label fw-bold">Insegnamento <span class="text-danger">*</span></label>
                                    <select name="ins_corso_id" id="ins_corso_id" class="form-select" required disabled>
                                        <option value="" disabled selected>-- Seleziona prima un corso --</option>
                                        @foreach($insegnamentiCorso as $ic)
                                            <option value="{{ $ic->ins_corso_id }}" data-corso-id="{{ $ic->corso_id }}">
                                                {{ $ic->insegnamento->nome ?? 'Insegnamento N/D' }} ({{ $ic->anno_di_corso }}° Anno)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 3. Seleziona Docente -->
                                <div class="mb-3">
                                    <label for="utente_id" class="form-label fw-bold">Docente <span class="text-danger">*</span></label>
                                    <select name="utente_id" id="utente_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona un docente --</option>
                                        @foreach($docenti as $d)
                                            <option value="{{ $d->utente_id }}">
                                                {{ $d->cognome }} {{ $d->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 4. Seleziona Ruolo -->
                                <div class="mb-4">
                                    <label for="ruolo" class="form-label fw-bold">Ruolo <span class="text-danger">*</span></label>
                                    <select name="ruolo" id="ruolo" class="form-select" required>
                                        <option value="Primario">Primario</option>
                                        <option value="Secondario">Secondario</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Salva Associazione
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Script di filtro dinamico inserito in public/js -->
        <script src="{{ asset('js/filtro-corso-insegnamento.js') }}"></script>
    </body>
</html>