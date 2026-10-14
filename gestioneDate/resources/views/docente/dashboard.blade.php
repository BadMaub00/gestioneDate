<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Docente</title>
        <link rel="stylesheet" href="{{ asset('css/docente.css') }}">
    </head>
    <body>
        <header>
            <h1>Dashboard Docente</h1>
            <nav>
                <ul>
                    <li><a href="{{ route('docente.dashboard') }}">Home</a></li>
                </ul>
            </nav>
        </header>
    </body>
</html>
