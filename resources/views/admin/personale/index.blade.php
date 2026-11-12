<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Personale Universitario - Università degli Studi di Brescia" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <!-- Contenuto Principale con Larghezza Ridotta -->
        <div class="container py-3" style="max-width: 900px;">
            
            <!-- Messaggi di Feedback -->
            <x-flash-messages />

            <!-- Titolo, Sottotitolo e Pulsante Torna alla Dashboard -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-people-fill me-2"></i>Personale Universitario
                    </h1>
                    <p class="text-muted small mb-0 mt-1">Elenco e gestione degli utenti registrati nel sistema</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase">
                    <i class="bi bi-arrow-left me-2"></i>TORNA ALLA DASHBOARD
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Blocco Strutturale Aggiungi Personale -->
            <div class="mb-4">
                <a href="{{ route('admin.personale.create') }}" class="btn btn-unibs text-white fw-bold w-100 py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center text-uppercase fs-6">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i> AGGIUNGI PERSONALE
                </a>
            </div>

            <!-- Tabella Personale (Omologata) -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Cognome</th>
                                <th class="py-3 px-4 text-dark fw-bold">Nome</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Ruolo</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personale as $p)
                                <tr>
                                    <td class="py-3 px-4 fw-semibold text-dark">{{ $p->cognome }}</td>
                                    <td class="py-3 px-4 fw-semibold text-dark">{{ $p->nome }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge border border-unibs text-unibs bg-transparent rounded-pill py-2 fw-bold d-inline-block" style="width: 140px;">
                                            {{ $p->ruolo }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Pulsante Circolare Modifica -->
                                            <a href="{{ route('admin.personale.edit', $p->utente_id) }}" class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Modifica Utente">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Pulsante Circolare Elimina con Modal Personalizzato -->
                                            <x-delete-form :action="route('admin.personale.destroy', $p->utente_id)" title="Elimina Utente">
                                                Sei sicuro di voler eliminare l'utente '{{ $p->nome }} {{ $p->cognome }}'?
                                            </x-delete-form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                        Nessun utente presente nel sistema.
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