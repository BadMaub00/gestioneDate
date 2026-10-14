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
