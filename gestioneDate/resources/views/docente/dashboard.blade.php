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
                    <form action="{{ route('docente.calendario') }}" method="GET">
                        <label for="sessione_id"><strong>Seleziona Sessione:</strong></label>
                        
                        <select name="sessione_id" id="sessione_id" required>
                            <option value="" disabled selected>-- Scegli la sessione --</option>
                            @foreach($sessioni as $sessione)
                                <option value="{{ $sessione->sessione_id }}">
                                    {{ $sessione->nome }} ({{ $sessione->anno }})
                                </option>
                            @endforeach
                        </select>

                        <button type="submit">Conferma</button>
                    </form>
                    
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-link" style="padding: 0; border: none; background: none; color: red; cursor: pointer;">
                            Logout
                        </button>
                    </form>

                </ul>
            </nav>
        </header>
    </body>
</html>
