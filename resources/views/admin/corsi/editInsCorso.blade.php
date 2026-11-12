<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Modifica Insegnamento Corso - ' . $corso->nome" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Si sono verificati degli errori durante la compilazione del modulo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <x-page-header icon="pencil-square" titolo="Modifica Insegnamento" :back-href="route('admin.corsi.piano_di_studi', $corso->corso_id)" back-label="Torna al Piano di Studi">
                <span class="badge border border-unibs text-unibs bg-transparent rounded-pill px-3 py-1.5 fw-bold mt-1">
                {{ $corso->nome }} ({{ $corso->tipo_corso }})
                </span>
            </x-page-header>

            <!-- Card Form di Modifica -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-unibs text-white fw-bold py-2">
                    <i class="bi bi-journal-text me-2"></i>Parametri Insegnamento
                </div>
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('admin.corsi.insegnamenti.update', $insegnamentoCorso->ins_corso_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Insegnamento (Solo Lettura) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-unibs">Insegnamento</label>
                            <input type="text" class="form-control bg-light text-muted" 
                                   value="{{ $insegnamentoCorso->insegnamento->nome ?? 'N/D' }} (Codice: {{ $insegnamentoCorso->insegnamento_id }})" disabled readonly>
                        </div>

                        <!-- Anno di corso -->
                        <div class="mb-3">
                            <label for="anno_di_corso" class="form-label fw-bold text-unibs">Anno di Corso</label>
                            <select name="anno_di_corso" id="anno_di_corso" class="form-select @error('anno_di_corso') is-invalid @enderror" required>
                                @for($a = 1; $a <= $durataAnni; $a++)
                                    <option value="{{ $a }}" {{ old('anno_di_corso', $insegnamentoCorso->anno_di_corso) == $a ? 'selected' : '' }}>
                                        {{ $a }}° Anno
                                    </option>
                                @endfor
                            </select>
                            @error('anno_di_corso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CFU -->
                        <div class="mb-3">
                            <label for="cfu" class="form-label fw-bold text-unibs">CFU</label>
                            <input type="number" name="cfu" id="cfu" class="form-control @error('cfu') is-invalid @enderror" 
                                   value="{{ old('cfu', $insegnamentoCorso->cfu) }}" min="1" max="30" required>
                            @error('cfu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ore / Durata -->
                        <div class="mb-4">
                            <label for="durata" class="form-label fw-bold text-unibs">Ore di Lezione (Durata)</label>
                            <input type="number" name="durata" id="durata" class="form-control @error('durata') is-invalid @enderror" 
                                   value="{{ old('durata', $insegnamentoCorso->durata) }}" min="1" required>
                            @error('durata')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bottoni Azione -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.corsi.piano_di_studi', $corso->corso_id) }}" class="btn btn-secondary rounded-pill px-4">Annulla</a>
                            <button type="submit" class="btn btn-unibs text-white fw-bold rounded-pill px-4">
                                <i class="bi bi-check-circle-fill me-1"></i>Salva Modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html>