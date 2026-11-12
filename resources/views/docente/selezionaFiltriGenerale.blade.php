<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Selezione Calendario Generale" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />


        <!-- Contenuto Principale -->
        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        
                        <!-- Card Selezione Filtri Calendario Generale -->
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2 fs-5"></i> Selezione Calendario Generale
                                </h5>
                            </div>

                            <div class="card-body p-4 bg-white">
                                <x-validation-errors />

                                <form action="{{ route('docente.procedicalendario_generale') }}" method="POST">
                                    @csrf

                                    <!-- 1. SELECT CORSO -->
                                    <div class="mb-3">
                                        <label for="corso_id" class="form-label fw-bold text-dark mb-1">
                                            1. Corso di Studi:
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" 
                                                id="corso_id" 
                                                name="corso_id" 
                                                data-url-template="{{ route('docente.api.anniCorso', ['corso_id' => ':id']) }}" 
                                                required>
                                            <option value="" selected disabled>-- Seleziona Corso --</option>
                                            @forelse ($corsi as $corso)
                                                <option value="{{ $corso->corso_id }}">{{ $corso->nome }}</option>
                                            @empty
                                                <option value="" disabled>Nessun corso disponibile</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- 2. SELECT ANNO DI CORSO -->
                                    <div class="mb-3">
                                        <label for="annoCorso" class="form-label fw-bold text-dark mb-1">
                                            2. Anno di Corso:
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="annoCorso" name="annoCorso" disabled required>
                                            <option value="" selected disabled>-- Seleziona prima un Corso --</option>
                                        </select>
                                    </div>

                                    <!-- 3. SELECT SESSIONE -->
                                    <div class="mb-4">
                                        <label for="sessione_id" class="form-label fw-bold text-dark mb-1">
                                            3. Sessione d'Esame (del corso scelto):
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="sessione_id" name="sessione_id" required disabled
                                                data-testo-vuoto="-- Seleziona prima un Corso --"
                                                data-testo-attivo="-- Seleziona Sessione --">
                                            <option value="" selected disabled>-- Seleziona prima un Corso --</option>
                                            @foreach ($sessioni as $sessione)
                                                <option value="{{ $sessione->sessione_id }}" data-corso="{{ $sessione->corso_id }}">
                                                    {{ $sessione->nome }} ({{ $sessione->anno }}) - [dal {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4 pt-2 border-top">
                                        <a href="{{ route('docente.dashboard') }}" class="btn btn-outline-secondary fw-bold px-3 py-2 rounded-3">
                                            <i class="bi bi-arrow-left me-1"></i> Annulla
                                        </a>
                                        <button type="submit" class="btn btn-unibs text-white fw-bold px-4 py-2 rounded-3 shadow-sm">
                                            Visualizza Calendario <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Script JS Esterno -->
        <script src="{{ asset('js/selezionaFiltriGenerale.js') }}"></script>
        <script src="{{ asset('js/sessioniPerCorso.js') }}"></script>
    </body>
</html>