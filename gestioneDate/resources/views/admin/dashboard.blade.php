<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Admin</title>
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    </head>
    <body>
        <header>
            <h1>Dashboard Admin</h1>
            <nav>
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-link" style="padding: 0; border: none; background: none; color: blue; cursor: pointer;">
                            Logout
                        </button>
                    </form>
                </ul>
            </nav>
        </header>
    </body>
</html>
