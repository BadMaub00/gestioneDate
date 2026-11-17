{{--
    Messaggio "nessun elemento" per liste e tabelle vuote. Il testo è lo slot.
    Uso:
        Riga di tabella:      <x-empty-state colspan="4" icon="inbox">Nessun utente presente nel sistema.</x-empty-state>
        Riga compatta:        <x-empty-state colspan="5" compact>Nessun appello fissato.</x-empty-state>
        Dentro una griglia:   <x-empty-state>Nessun corso presente nel sistema.</x-empty-state>
    "icon" è il nome dell'icona Bootstrap (senza "bi-"); nella variante compatta, se omesso, usa "info-circle".
--}}
@props(['colspan' => null, 'icon' => null, 'compact' => false])

@if($colspan)
    <tr>
        @if($compact)
            <td colspan="{{ $colspan }}" class="text-center py-4 text-muted fw-medium">
                <i class="bi bi-{{ $icon ?? 'info-circle' }} me-1 text-secondary"></i> {{ $slot }}
            </td>
        @else
            <td colspan="{{ $colspan }}" class="text-center text-muted py-5 fs-6">
                @if($icon)<i class="bi bi-{{ $icon }} fs-3 d-block mb-2 text-secondary"></i>@endif
                {{ $slot }}
            </td>
        @endif
    </tr>
@else
    <div class="col-12 text-center py-5">
        @if($icon)<i class="bi bi-{{ $icon }} fs-3 d-block mb-2 text-secondary"></i>@endif
        <p class="text-muted fs-5">{{ $slot }}</p>
    </div>
@endif
