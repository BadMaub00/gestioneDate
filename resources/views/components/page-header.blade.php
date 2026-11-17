{{--
    Intestazione di pagina (liste e pagine principali): titolo con icona, pulsante "torna" a destra e linea di separazione.
    Il contenuto dello slot (opzionale) viene messo sotto il titolo: sottotitolo, badge, ecc.
    Uso: <x-page-header icon="people-fill" titolo="Personale" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" />
    Opzioni:
        upper  -> pulsante "torna" in maiuscolo
        stack  -> su schermi piccoli titolo e pulsante vanno uno sopra l'altro
--}}
@props(['icon', 'titolo', 'backHref', 'backLabel', 'upper' => false, 'stack' => false])

<div class="d-flex {{ $stack ? 'flex-column flex-md-row align-items-md-center gap-3' : 'align-items-center' }} justify-content-between mb-4">
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
    <x-back-button :href="$backHref" :label="$backLabel" :upper="$upper" />
</div>
<hr class="border-unibs border-2 opacity-100 mb-4">
