{{--
    Pulsante a tutta larghezza "Aggiungi ..." posto sopra le liste dell'area admin (già con il margine inferiore).
    Uso:
        <x-add-button :href="route('admin.corsi.create')">Aggiungi Corso</x-add-button>
        <x-add-button :href="route('admin.personale.associa')" icon="person-plus-fill">Aggiungi Associazione</x-add-button>
    Opzioni: upper (testo maiuscolo), large (pulsante più alto).
--}}
@props(['href', 'icon' => 'plus-circle-fill', 'upper' => false, 'large' => false])

<div class="mb-4">
    <a href="{{ $href }}"
       class="btn btn-unibs text-white fw-bold w-100 {{ $large ? 'py-3 fs-6' : 'py-2.5' }} rounded-3 shadow-sm d-flex align-items-center justify-content-center{{ $upper ? ' text-uppercase' : '' }}">
        <i class="bi bi-{{ $icon }} me-2 fs-5"></i>{{ $slot }}
    </a>
</div>
