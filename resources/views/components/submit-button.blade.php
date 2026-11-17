{{--
    Pulsante di invio dei form (Salva / Aggiorna / Crea ...), a pillola blu UniBS.
    Uso:
        <x-submit-button icon="save">Salva Aula</x-submit-button>
        <x-submit-button icon="pencil-square" class="mt-2">Aggiorna Sessione</x-submit-button>
        <x-submit-button icon="upload" :block="false">Importa</x-submit-button>   (non a tutta larghezza)
--}}
@props(['icon' => null, 'block' => true])

<button type="submit" {{ $attributes->merge(['class' => 'btn btn-unibs text-white fw-bold rounded-pill shadow-sm ' . ($block ? 'w-100 py-2' : 'px-4')]) }}>
    @if($icon)<i class="bi bi-{{ $icon }} {{ $block ? 'me-2' : 'me-1' }}"></i>@endif{{ $slot }}
</button>
