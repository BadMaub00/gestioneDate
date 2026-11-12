<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Gestione Aule - ' . $edificio->nome" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <script src="{{ asset('js/confirmDelete.js') }}"></script>

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">

            <x-flash-messages />

            <!-- Intestazione -->
            <x-page-header icon="geo-alt-fill" :titolo="'Aule dell\'Edificio: ' . $edificio->nome" :back-href="route('admin.sedi.edifici')" back-label="Torna agli Edifici">
                <p class="text-muted small m-0 mt-1">Gestione e consultazione delle aule disponibili in questo edificio</p>
            </x-page-header>

            <!-- Pulsante Aggiungi Aula -->
            <div class="mb-4">
                <a href="{{ route('admin.sedi.createAula', $edificio->edificio_id) }}" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Aggiungi Aula
                </a>
            </div>

            <!-- Tabella Aule -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Nome Aula</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Capienza</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aule as $aula)
                                <tr>
                                    <!-- Nome Aula -->
                                    <td class="py-3 px-4 fw-bold text-dark">
                                        {{ $aula->nome }}
                                    </td>

                                    <!-- Capienza -->
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge border border-unibs text-unibs bg-transparent px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="bi bi-person-fill me-1"></i>{{ $aula->capienza ?? 'N/D' }} posti
                                        </span>
                                    </td>

                                    <!-- Azioni -->
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Tasto Modifica -->
                                            <a href="{{ route('admin.sedi.editAula', $aula->aula_id) }}" 
                                               class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 36px; height: 36px;" 
                                               title="Modifica Aula">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Tasto Elimina -->
                                            <x-delete-form :action="route('admin.sedi.destroyAula', $aula->aula_id)" title="Elimina Aula">
                                                Sei sicuro di voler eliminare l'aula '{{ $aula->nome }}'?
                                            </x-delete-form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-building-exclamation fs-3 d-block mb-2 text-secondary"></i>
                                        Nessuna aula presente per questo edificio.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </body>
</html>