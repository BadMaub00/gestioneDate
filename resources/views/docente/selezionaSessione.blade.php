<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Dashboard Docente" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        
                        <!-- Card Selezione Sessione -->
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar-range-fill me-2 fs-5"></i> Selezione Sessione d'Esame
                                </h5>
                            </div>

                            <div class="card-body p-4 bg-white">
                                <x-validation-errors />

                                <form action="{{ route('docente.procediCalendario') }}" method="POST">
                                    @csrf

                                    <!-- 1. SELECT CORSO -->
                                    <div class="mb-3">
                                        <label for="corso_id" class="form-label fw-bold text-dark mb-2">
                                            1. Corso di Studi:
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="corso_id" name="corso_id" required>
                                            <option value="" selected disabled>-- Seleziona Corso --</option>
                                            @forelse($corsi as $corso)
                                                <option value="{{ $corso->corso_id }}" {{ old('corso_id') == $corso->corso_id ? 'selected' : '' }}>
                                                    {{ $corso->nome }} - {{ $corso->tipo_corso }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Nessun corso disponibile</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- 2. SELECT SESSIONE (dipende dal corso) -->
                                    <div class="mb-4">
                                        <label for="sessione_id" class="form-label fw-bold text-dark mb-2">
                                            2. Sessione d'Esame:
                                        </label>
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="sessione_id" name="sessione_id" required disabled
                                                data-testo-vuoto="-- Seleziona prima un Corso --"
                                                data-testo-attivo="-- Seleziona Sessione --">
                                            <option value="" selected disabled>-- Seleziona prima un Corso --</option>
                                            @foreach($sessioni as $sessione)
                                                <option value="{{ $sessione->sessione_id }}" data-corso="{{ $sessione->corso_id }}" {{ old('sessione_id') == $sessione->sessione_id ? 'selected' : '' }}>
                                                    {{ $sessione->nome }} ({{ $sessione->anno }}) - [dal {{ \Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y') }}] · scadenza inserimento: {{ $sessione->scadenza_effettiva->format('d/m/Y') }}
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

            <script src="{{ asset('js/sessioniPerCorso.js') }}"></script>
    </body>
</html>