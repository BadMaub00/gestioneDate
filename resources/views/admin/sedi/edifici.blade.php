<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Seleziona Edificio - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <script src="{{ asset('js/confirmDelete.js') }}"></script>

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">

            <x-flash-messages />

            <!-- Intestazione e Pulsante Torna alla Dashboard -->
            <x-page-header icon="building" titolo="Seleziona un Edificio" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" />

            <!-- Pulsante Aggiungi Edificio -->
            <div class="mb-4">
                <a href="{{ route('admin.sedi.createEdificio') }}" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Aggiungi Edificio
                </a>
            </div>

            <!-- Griglia degli Edifici -->
            <div class="row g-4">
                @forelse($edifici as $edificio)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-between p-4 bg-white">
                                <div>
                                    <!-- Titolo e Bottoni Azione -->
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                        <h5 class="card-title fw-bold text-unibs m-0 pe-2">{{ $edificio->nome }}</h5>
                                        
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <!-- Tasto Modifica -->
                                            <a href="{{ route('admin.sedi.editEdificio', $edificio->edificio_id) }}" 
                                               class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 36px; height: 36px;" 
                                               title="Modifica Edificio">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Tasto Elimina -->
                                            <x-delete-form :action="route('admin.sedi.destroyEdificio', $edificio->edificio_id)" title="Elimina Edificio">
                                                Sei sicuro di voler eliminare l'edificio '{{ $edificio->nome }}'? Verranno eliminate anche tutte le aule associate.
                                            </x-delete-form>
                                        </div>
                                    </div>

                                    <!-- Indirizzo / Via dell'Edificio -->
                                    <p class="text-muted small mb-3 fw-medium">
                                        <i class="bi bi-geo-alt-fill me-1 text-unibs"></i>{{ $edificio->via ?? $edificio->indirizzo ?? 'Indirizzo non specificato' }}
                                    </p>
                                </div>

                                <a href="{{ route('admin.sedi.aule', $edificio->edificio_id) }}" class="btn btn-unibs text-white fw-bold w-100 rounded-pill shadow-sm mt-3">
                                    Visualizza Aule <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Nessun edificio presente nel sistema.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </body>
</html>