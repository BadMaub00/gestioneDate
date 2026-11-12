{{--
    Intestazione di pagina: titolo con icona, pulsante "torna" a destra e linea di separazione.
    Il contenuto dello slot (opzionale) viene messo sotto il titolo: sottotitolo, badge, ecc.
    Uso: <x-page-header icon="people-fill" titolo="Personale" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" />
--}}
@props(['icon', 'titolo', 'backHref', 'backLabel'])

<div class="d-flex align-items-center justify-content-between mb-4">
    @if($slot->isEmpty())
        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
            <i class="bi bi-{{ $icon }} me-2"></i>{{ $titolo }}
        </h1>
    @else
        <div>
            <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                <i class="bi bi-{{ $icon }} me-2"></i>{{ $titolo }}
            </h1>
            {{ $slot }}
        </div>
    @endif
    <a href="{{ $backHref }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i>{{ $backLabel }}
    </a>
</div>
<hr class="border-unibs border-2 opacity-100 mb-4">
