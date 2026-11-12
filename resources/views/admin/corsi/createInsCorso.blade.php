<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Aggiungi Insegnamento - ' . $corso->nome" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-person-plus-fill me-2"></i>Aggiungi Insegnamento al Corso
                        </h1>
                        <a href="{{ route('admin.corsi.piano_di_studi', $corso->corso_id) }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="{{ route('admin.corsi.insegnamenti.store', $corso->corso_id) }}" method="POST">
                                @csrf

                                <!-- Insegnamento -->
                                <div class="mb-3">
                                    <label for="insegnamento_id" class="form-label fw-bold text-dark small mb-1">
                                        Seleziona Insegnamento <span class="text-danger">*</span>
                                    </label>
                                    <select name="insegnamento_id" id="insegnamento_id" class="form-select shadow-sm" required>
                                        <option value="" disabled selected>-- Seleziona Insegnamento --</option>
                                        @foreach($insegnamenti as $insegnamento)
                                            <option value="{{ $insegnamento->insegnamento_id }}" {{ old('insegnamento_id') == $insegnamento->insegnamento_id ? 'selected' : '' }}>
                                                {{ $insegnamento->nome }} (Codice: {{ $insegnamento->insegnamento_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Anno di Corso -->
                                <div class="mb-3">
                                    <label for="anno_di_corso" class="form-label fw-bold text-dark small mb-1">
                                        Anno di Corso <span class="text-danger">*</span>
                                    </label>
                                    <select name="anno_di_corso" id="anno_di_corso" class="form-select shadow-sm" required>
                                        <option value="" disabled selected>-- Seleziona Anno --</option>
                                        @php
                                            $durataAnni = match($corso->tipo_corso ?? '') {
                                                'Magistrale' => 2,
                                                'Magistrale a ciclo unico' => 5,
                                                default => 3,
                                            };
                                        @endphp
                                        @for($a = 1; $a <= $durataAnni; $a++)
                                            <option value="{{ $a }}" {{ old('anno_di_corso') == $a ? 'selected' : '' }}>
                                                {{ $a }}° Anno
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="row g-3 mb-4">
                                    <!-- CFU -->
                                    <div class="col-6">
                                        <label for="cfu" class="form-label fw-bold text-dark small mb-1">
                                            CFU <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               name="cfu" 
                                               id="cfu" 
                                               class="form-control shadow-sm" 
                                               placeholder="es. 6" 
                                               min="1" 
                                               value="{{ old('cfu') }}" 
                                               required>
                                    </div>

                                    <!-- Durata in Ore -->
                                    <div class="col-6">
                                        <label for="durata" class="form-label fw-bold text-dark small mb-1">
                                            Durata (Ore) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               name="durata" 
                                               id="durata" 
                                               class="form-control shadow-sm" 
                                               placeholder="es. 48" 
                                               min="1" 
                                               value="{{ old('durata') }}" 
                                               required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i>Salva Insegnamento
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>