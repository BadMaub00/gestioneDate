<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Seleziona Corso" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">

            <x-flash-messages />

            <x-page-header icon="mortarboard-fill" titolo="Seleziona un Corso di Laurea" :back-href="route('admin.dashboard')" back-label="Torna alla Dashboard" />

            <!-- Pulsante Aggiungi Corso -->
            <x-add-button :href="route('admin.corsi.create')">Aggiungi Corso</x-add-button>

            <!-- Griglia dei Corsi -->
            <div class="row g-4">
                @forelse($corsi as $corso)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <!-- Titolo e Bottoni Azione in alto a destra -->
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                        <h5 class="card-title fw-bold text-unibs m-0 pe-2">{{ $corso->nome }}</h5>
                                        
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <!-- Tasto Modifica -->
                                            <x-edit-button :href="route('admin.corsi.edit', $corso->corso_id)" title="Modifica Corso" />

                                            <!-- Tasto Elimina con Modal Personalizzato -->
                                            <x-delete-form :action="route('admin.corsi.destroy', $corso)" title="Elimina Corso">
                                                Sei sicuro di voler eliminare il corso '{{ $corso->nome }}'? Verranno rimossi anche i relativi piani di studio associati.
                                            </x-delete-form>
                                        </div>
                                    </div>

                                    <!-- Tipologia del Corso -->
                                    <p class="text-muted small mb-3 fw-medium">
                                        <i class="bi bi-mortarboard me-1 text-unibs"></i>{{ $corso->tipo_corso }}
                                    </p>
                                </div>

                                <a href="{{ route('admin.corsi.piano_di_studi', $corso->corso_id) }}" class="btn btn-unibs text-white fw-bold w-100 rounded-pill shadow-sm mt-3">
                                    Visualizza Piano di Studi <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-empty-state>Nessun corso presente nel sistema.</x-empty-state>
                @endforelse
            </div>
        </div>

        <!-- Script Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>