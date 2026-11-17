{{--
    Form di eliminazione con pulsante circolare a cestino. La conferma è gestita da public/js/confirmDelete.js
    tramite l'attributo data-confirm, che prende come testo il contenuto dello slot.
    Uso:
        <x-delete-form :action="route('admin.corsi.destroy', $corso)" title="Elimina Corso">
            Sei sicuro di voler eliminare il corso '{{ $corso->nome }}'?
        </x-delete-form>
    Con "small" il pulsante è la variante più piccola (32px) usata nelle tabelle compatte.
    Con "disabled" mostra invece un cestino grigio non cliccabile (action e slot non servono),
    dove "title" è il motivo per cui l'eliminazione non è possibile:
        <x-delete-form title="Non è possibile eliminare appelli di sessioni passate" disabled />
--}}
@props(['action' => null, 'title', 'small' => false, 'disabled' => false])

@php
    $size = $small ? '32px' : '36px';
@endphp

@if($disabled)
    <button type="button"
            {{ $attributes->merge(['class' => 'btn bg-white text-secondary border border-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm opacity-50']) }}
            disabled
            @style(["width: $size", "height: $size"])
            title="{{ $title }}">
        <i class="bi bi-trash-fill" @style(['font-size: 0.85rem' => $small])></i>
    </button>
@else
    <form action="{{ $action }}" method="POST" @class(['d-inline', 'm-0', 'p-0' => $small]) data-confirm="{{ $slot }}">
        @csrf
        @method('DELETE')
        <button type="submit"
                {{ $attributes->merge(['class' => 'btn btn-delete bg-white text-danger border border-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm']) }}
                @style(["width: $size", "height: $size"])
                title="{{ $title }}">
            <i class="bi bi-trash-fill" @style(['font-size: 0.85rem' => $small])></i>
        </button>
    </form>
@endif