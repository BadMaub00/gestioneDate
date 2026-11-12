{{--
    Navbar istituzionale UniBS, identica per tutte le pagine autenticate.
    Il pulsante Home e il logo portano alla dashboard del ruolo dell'utente loggato
    (Amministratore o Docente), vedi Utente::dashboardRoute().
    Uso: <x-navbar />
--}}
@php
    $dashboardUrl = Auth::user()?->dashboardRoute() ?? route('login');
@endphp

<nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
    <div class="container-fluid px-3 px-md-5">

        <!-- Logo Università -->
        <a class="navbar-brand py-0" href="{{ $dashboardUrl }}">
            <img class="img-logo" src="{{ asset('images/unibsLogo.png') }}" alt="Unibs" style="max-height: 70px;">
        </a>

        <!-- Toggler Mobile -->
        <button class="navbar-toggler border-0 shadow-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu di Navigazione -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start"
                       @if(url()->current() === $dashboardUrl) aria-current="page" @endif
                       href="{{ $dashboardUrl }}" style="width: 115px;">
                        <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit"
                                class="btn btn-danger btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start"
                                style="width: 115px;">
                            <i class="bi bi-box-arrow-right text-center me-2" style="width: 18px;"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
