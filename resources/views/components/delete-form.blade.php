{{--
    Form di eliminazione con pulsante circolare a cestino. La conferma è gestita da public/js/confirmDelete.js
    tramite l'attributo data-confirm, che prende come testo il contenuto dello slot.
    Uso:
        <x-delete-form :action="route('admin.corsi.destroy', $corso)" title="Elimina Corso">
            Sei sicuro di voler eliminare il corso '{{ $corso->nome }}'?
        </x-delete-form>
    Con "small" il pulsante è la variante più piccola (32px) usata nelle tabelle compatte.
--}}
@props(['action', 'title', 'small' => false])

<form action="{{ $action }}" method="POST" class="{{ $small ? 'd-inline m-0 p-0' : 'd-inline m-0' }}" data-confirm="{{ $slot }}">
    @csrf
    @method('DELETE')
    @if($small)
        <button type="submit"
                class="btn btn-white text-danger border border-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-sm"
                style="width: 32px; height: 32px;"
                title="{{ $title }}">
            <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
        </button>
    @else
        <button type="submit"
                class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                style="width: 36px; height: 36px;"
                title="{{ $title }}">
            <i class="bi bi-trash-fill"></i>
        </button>
    @endif
</form>
