{{-- <head> comune a tutte le pagine. Uso: <x-html-head titolo="Titolo pagina" /> --}}
@props(['titolo'])

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titolo }}</title>

    <!-- CSS Bootstrap & Icone -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap_v2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">

    <!-- CSS Personalizzati -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
