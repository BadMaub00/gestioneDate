{{--
    Pulsante "torna indietro" con freccia. Di default è il pulsante a pillola blu UniBS.
    Uso:
        <x-back-button :href="route('admin.corsi.index')" />   ("Torna Indietro")
        <x-back-button :href="route('admin.dashboard')" label="Torna alla Dashboard" upper />
        <x-back-button :href="route('docente.dashboard')" label="Dashboard" outline />   (stile outline usato dalle pagine docente)
    "upper" mostra il testo in maiuscolo (come nelle intestazioni delle liste) e rende il pulsante un po' più alto.
--}}
@props(['href', 'label' => 'Torna Indietro', 'upper' => false, 'outline' => false])

@if($outline)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-outline-unibs fw-bold rounded-3 shadow-sm px-3 py-2']) }}>
        <i class="bi bi-arrow-left me-1"></i> {{ $label }}
    </a>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm' . ($upper ? ' py-2 d-inline-flex align-items-center text-uppercase text-nowrap' : '')]) }}>
        <i class="bi bi-arrow-left {{ $upper ? 'me-2' : 'me-1' }}"></i>{{ $label }}
    </a>
@endif
