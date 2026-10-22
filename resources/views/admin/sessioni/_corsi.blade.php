{{-- Selezione multipla dei corsi di studi a cui assegnare la sessione.
     Variabili: $corsi (collection di Corso), $selezionati (array di corso_id già scelti) --}}
@php
    $selezionati = collect(old('corsi', $selezionati ?? []))->map(fn ($c) => (int) $c)->all();
@endphp

<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <label class="form-label fw-bold m-0">Corsi di Studi <span class="text-danger">*</span></label>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="selezionaTuttiCorsi">Seleziona tutti</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="deselezionaTuttiCorsi">Deseleziona</button>
        </div>
    </div>

    <div class="border rounded-3 p-2 bg-white" style="max-height: 220px; overflow-y: auto;">
        @forelse($corsi as $corso)
            <div class="form-check py-1">
                <input class="form-check-input corso-checkbox" type="checkbox" name="corsi[]"
                       id="corso_{{ $corso->corso_id }}" value="{{ $corso->corso_id }}"
                       {{ in_array((int) $corso->corso_id, $selezionati, true) ? 'checked' : '' }}>
                <label class="form-check-label" for="corso_{{ $corso->corso_id }}">
                    {{ $corso->nome }} <span class="text-muted small">- {{ $corso->tipo_corso }}</span>
                </label>
            </div>
        @empty
            <div class="text-muted small p-2">Nessun corso di studi presente: creane uno prima di aggiungere una sessione.</div>
        @endforelse
    </div>
    <div class="form-text small">La sessione viene creata per ciascun corso selezionato. Tra due sessioni dello stesso corso deve passare almeno un mese.</div>
</div>
