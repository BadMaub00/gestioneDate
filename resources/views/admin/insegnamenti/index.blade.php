<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Gestione Insegnamenti - Amministratore" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale con Larghezza Ridotta -->
        <div class="container py-3" style="max-width: 900px;">

            <x-flash-messages />

            <!-- Intestazione -->
            <x-page-header icon="journal-bookmark-fill" titolo="Gestione Insegnamenti" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard">
                <p class="text-muted small m-0 mt-1">Elenco di tutti gli insegnamenti registrati a sistema</p>
            </x-page-header>

            <!-- Pulsante Aggiungi Insegnamento -->
            <div class="mb-4">
                <a href="{{ route('admin.insegnamenti.create') }}" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Aggiungi Insegnamento
                </a>
            </div>

            <!-- Tabella Insegnamenti -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Nome Insegnamento</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold" style="width: 140px;">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($insegnamenti as $insegnamento)
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-dark">
                                        {{ $insegnamento->nome }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Modifica -->
                                            <a href="{{ route('admin.insegnamenti.edit', $insegnamento->insegnamento_id) }}" 
                                               class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 36px; height: 36px;" 
                                               title="Modifica Insegnamento">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Elimina con Modal Personalizzato -->
                                            <x-delete-form :action="route('admin.insegnamenti.destroy', $insegnamento->insegnamento_id)" title="Elimina Insegnamento">
                                                Sei sicuro di voler eliminare l'insegnamento '{{ $insegnamento->nome }}'?
                                            </x-delete-form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-journal-x fs-3 d-block mb-2 text-secondary"></i>
                                        Nessun insegnamento presente nel sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Script Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>