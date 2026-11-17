{{--
    Pulsante circolare a matita per la modifica, gemello di <x-delete-form>.
    Uso:
        <x-edit-button :href="route('admin.corsi.edit', $corso->corso_id)" title="Modifica Corso" />
    Con "small" il pulsante è la variante più piccola (32px) usata nelle tabelle compatte.
    Con "disabled" viene mostrato un pulsante grigio non cliccabile: "title" diventa il motivo
    per cui la modifica non è possibile (href non serve).
    Classi aggiuntive (es. ms-2) si passano normalmente con class="...".
--}}
@props(['href' => '#', 'title', 'small' => false, 'disabled' => false])

@php
    $size = $small ? '32px' : '36px';
@endphp

@if($disabled)
    <button type="button"
            {{ $attributes->merge(['class' => 'btn bg-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50']) }}
            disabled
            @style(["width: $size", "height: $size"])
            title="{{ $title }}">
        <i class="bi bi-pencil-fill" @style(['font-size: 0.85rem' => $small])></i>
    </button>
@else
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => 'btn btn-change bg-white text-unibs border border-unibs rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm']) }}
       @style(["width: $size", "height: $size"])
       title="{{ $title }}">
        <i class="bi bi-pencil-fill" @style(['font-size: 0.85rem' => $small])></i>
    </a>
@endif