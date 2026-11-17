{{--
    Intestazione delle pagine con form (crea/modifica): titolo a sinistra e pulsante "Torna Indietro" a destra.
    Lo slot (opzionale) finisce sotto il titolo, ad esempio per un sottotitolo.
    Uso:
        <x-form-header titolo="Modifica Corso di Laurea" :back-href="route('admin.corsi.index')" />
        <x-form-header titolo="Importa da Excel" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" />
    Con "icon" il titolo diventa la variante grande con icona; "mb" cambia il margine inferiore (default 3).
--}}
@props(['titolo', 'backHref', 'backLabel' => 'Torna Indietro', 'icon' => null, 'mb' => 3])

@php
    $h1Class = $icon ? 'h3 fw-bold text-unibs m-0 d-flex align-items-center' : 'h4 fw-bold text-unibs m-0';
@endphp

<div class="d-flex align-items-center justify-content-between mb-{{ $mb }}">
    @if($slot->isEmpty())
        <h1 class="{{ $h1Class }}">@if($icon)<i class="bi bi-{{ $icon }} me-2"></i>@endif{{ $titolo }}</h1>
    @else
        <div>
            <h1 class="{{ $h1Class }}">@if($icon)<i class="bi bi-{{ $icon }} me-2"></i>@endif{{ $titolo }}</h1>
            {{ $slot }}
        </div>
    @endif
    <x-back-button :href="$backHref" :label="$backLabel" />
</div>
