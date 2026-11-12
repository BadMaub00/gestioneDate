{{-- Pulsante a tutta larghezza "Torna alla Dashboard" (dashboard del ruolo loggato). Uso: <x-back-dashboard /> --}}
<div class="row mt-4">
    <div class="col-12">
        <a href="{{ Auth::user()->dashboardRoute() }}"
           class="btn btn-outline-unibs fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
            <i class="bi bi-arrow-left me-2 fs-6"></i>Torna alla Dashboard
        </a>
    </div>
</div>
