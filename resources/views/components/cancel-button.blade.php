{{--
    Pulsante "Annulla" che riporta alla pagina precedente senza salvare.
    Uso:
        <x-cancel-button :href="route('docente.appelliInsegnamento', $id)" />
        <x-cancel-button :href="..." pill />   (stile admin, a pillola grigia piena)
        <x-cancel-button :href="..." arrow />   (con freccia a sinistra, come nelle pagine di selezione docente)
    Il testo si cambia con lo slot: <x-cancel-button :href="...">Indietro</x-cancel-button>
--}}
@props(['href', 'pill' => false, 'arrow' => false])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => $pill
        ? 'btn btn-secondary rounded-pill px-4'
        : 'btn btn-outline-secondary fw-bold rounded-3 ' . ($arrow ? 'px-3 py-2' : 'px-4')]) }}>
    @if($arrow)<i class="bi bi-arrow-left me-1"></i> @endif{{ $slot->isEmpty() ? 'Annulla' : $slot }}
</a>
