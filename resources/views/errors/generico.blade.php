{{-- Pagina d'errore generica (sessione scaduta, accesso negato, pagina inesistente, errore del server...).
     Stesso layout del login. Il pulsante fa un logout sicuro (POST + CSRF) e porta alla schermata di accesso. --}}
<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Errore - Gestione Date d'Esame" />
    <body class="min-vh-100 d-flex flex-column justify-content-between" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <div class="my-auto py-4">
            <div class="container text-center">

                <div class="mb-4">
                    <img class="img-fluid"
                         src="{{ asset('images/unibsLogo.png') }}"
                         alt="Unibs"
                         style="max-height: 110px;">
                </div>

                <h1 class="h2 fw-bold text-unibs mb-4">Ops... si è verificato un errore!</h1>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 px-4 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-box-arrow-in-left me-2 fs-5"></i> Torna alla schermata di accesso
                    </button>
                </form>

            </div>
        </div>

        <footer class="py-3 text-center small">
            <div class="container">
                <span class="fw-semibold text-dark">&copy; Università degli Studi di Brescia - Portale del Personale Universitario</span>
            </div>
        </footer>

    </body>
</html>
