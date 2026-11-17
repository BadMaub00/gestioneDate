{{--
    Intestazione blu UniBS delle card, con icona e titolo.
    Uso:
        <x-card-header icon="calendar3" titolo="Selezione Calendario Generale" />
        <x-card-header icon="file-earmark-plus" titolo="Dettagli Appello" size="sm" />
        <x-card-header icon="calendar3" :titolo="$anno . '° Anno di Corso'" size="xs" />
    size: md (default, titolo e icona grandi) | sm (titolo piccolo, usato nei form docente) | xs (barra sottile senza h5).
--}}
@props(['icon', 'titolo', 'size' => 'md'])

@if($size === 'xs')
    <div {{ $attributes->merge(['class' => 'card-header bg-unibs text-white fw-bold py-2']) }}>
        <i class="bi bi-{{ $icon }} me-2"></i>{{ $titolo }}
    </div>
@elseif($size === 'sm')
    <div {{ $attributes->merge(['class' => 'card-header bg-unibs text-white py-3 px-4']) }}>
        <h5 class="card-title mb-0 fw-bold fs-6">
            <i class="bi bi-{{ $icon }} me-2"></i>{{ $titolo }}
        </h5>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'card-header bg-unibs text-white py-3 px-4 border-0']) }}>
        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
            <i class="bi bi-{{ $icon }} me-2 fs-5"></i> {{ $titolo }}
        </h5>
    </div>
@endif
