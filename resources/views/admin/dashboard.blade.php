<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Dashboard Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="py-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">

                        <!-- Titolo Intestazione Amministratore Dinamico -->
                        <div class="mb-4">
                            <h1 class="h3 fw-bold text-unibs mt-3 mb-2 d-flex align-items-center">
                                <i class="bi bi-person-badge me-2"></i>Amministratore: {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                            </h1>
                            <hr class="border-unibs border-2 opacity-100 my-0">
                        </div>

                        <!-- Riquadri (Centrati nello schermo e più ampi) -->
                        <div class="mx-auto mb-5" style="max-width: 960px;">
                            <div class="row g-3 justify-content-center">

                                <!-- 1. Corsi -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.corsi.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">   
                                        <i class="bi bi-mortarboard-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Corsi</span>
                                    </a>
                                </div>

                                <!-- 2. Insegnamenti -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.insegnamenti.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-journal-bookmark-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Insegnamenti</span>
                                    </a>
                                </div>

                                <!-- 3. Personale Universitario -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.personale.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-people-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Personale universitario</span>
                                    </a>
                                </div>

                                <!-- 4. Associa -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.personale.associazioni_index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-people-fill text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Incarichi Docenti</span>
                                    </a>
                                </div>

                                <!-- 5. Appelli -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.calendario') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-calendar-check text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Appelli</span>
                                    </a>
                                </div>

                                <!-- 6. Sessioni -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.sessioni.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-calendar-range text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Sessioni</span>
                                    </a>
                                </div>

                                <!-- 7. Sedi (Posizionato al centro della terza fila) -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.sedi.edifici') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-building text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Sedi</span>
                                    </a>
                                </div>

                                <!-- 8. Importa da Excel -->
                                <div class="col-12 col-sm-4">
                                    <a href="{{ route('admin.import.index') }}" class="card shadow-sm border-0 rounded-3 text-decoration-none text-dark h-100 p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="bi bi-file-earmark-excel text-unibs fs-1 mb-2"></i>
                                        <span class="fw-semibold">Importa da Excel</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                    </diSv>
                </div>
            </div>
        </div>

    </body>
</html>