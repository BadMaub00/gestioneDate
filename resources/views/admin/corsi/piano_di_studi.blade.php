<!DOCTYPE html>
<html lang="it">
    <x-html-head :titolo="'Piano di Studi - ' . $corso->nome" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale -->
        <div class="container py-3">

            <x-flash-messages />

            <x-page-header icon="journal-bookmark-fill" :titolo="'Piano di Studi: ' . $corso->nome" :back-href="route('admin.corsi.index')" back-label="Torna ai Corsi">
                <span class="badge border border-unibs text-unibs bg-transparent rounded-pill px-3 py-1.5 fw-bold mt-1">{{ $corso->tipo_corso }}</span>
            </x-page-header>

            <!-- Pulsante Aggiungi Insegnamento al Corso -->
            <div class="mb-4">
                <a href="{{ route('admin.corsi.insegnamenti.create', $corso->corso_id) }}" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Aggiungi insegnamenti al corso
                </a>
            </div>

            <!-- Anni del corso -->
            @for ($anno = 1; $anno <= $durataAnni; $anno++)
                <div class="card shadow-sm mb-4 border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-unibs text-white fw-bold py-2">
                        <i class="bi bi-calendar3 me-2"></i>{{ $anno }}° Anno di Corso
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($insegnamentiPerAnno->get($anno, []) as $ic)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 {{ $loop->even ? 'bg-light' : '' }}">
                                    <div>
                                        <strong class="fs-6 text-dark">{{ $ic->insegnamento->nome ?? 'Insegnamento N/D' }}</strong>
                                        <span class="text-muted d-block small">Codice: {{ $ic->insegnamento_id }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge border border-unibs text-unibs bg-transparent rounded-pill px-3 py-2 fw-bold">
                                            {{ $ic->cfu }} CFU
                                        </span>
                                        <span class="badge border border-unibs text-unibs bg-transparent rounded-pill px-3 py-2 fw-bold">
                                            {{ $ic->durata }} ore
                                        </span>

                                        <!-- Tasto Modifica -->
                                        <a href="{{ route('admin.corsi.insegnamenti.edit', $ic->ins_corso_id) }}" 
                                           class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center ms-2" 
                                           style="width: 36px; height: 36px;" 
                                           title="Modifica Insegnamento">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <!-- Tasto Elimina con Modal Personalizzato -->
                                        <x-delete-form :action="route('admin.corsi.insegnamenti.destroy', $ic->ins_corso_id)" title="Rimuovi Insegnamento">
                                            Sei sicuro di voler rimuovere l'insegnamento '{{ $ic->insegnamento->nome ?? '' }}' dal piano di studi?
                                        </x-delete-form>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted fst-italic py-3 px-4">
                                    Nessun insegnamento inserito per il {{ $anno }}° anno.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Script Modal di Conferma Eliminazione Personalizzato -->
        <script src="{{ asset('js/confirmDelete.js') }}"></script>

    </body>
</html>