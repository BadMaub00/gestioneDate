<!DOCTYPE html>
<html lang="it">
    <x-html-head titolo="Modifica Sessione d'Esame" />
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <x-bootstrap-scripts />

        <x-navbar />

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <x-form-header titolo="Modifica Sessione" :back-href="route('admin.sessioni.index')" />

                    <x-validation-errors />

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.sessioni.update', $sessione->sessione_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome Sessione <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" required value="{{ old('nome', $sessione->nome) }}">
                                </div>

                                @include('admin.sessioni._corsi', ['corsi' => $corsi, 'selezionati' => $corsiSelezionati])

                                <div class="mb-3">
                                    <label for="numero_appelli" class="form-label fw-bold">N° Appelli Previsti (1-4) <span class="text-danger">*</span></label>
                                    <input type="number" name="numero_appelli" id="numero_appelli" class="form-control" min="1" max="4" required value="{{ old('numero_appelli', $sessione->numero_appelli) }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_inizio" class="form-label fw-bold">Data Inizio <span class="text-danger">*</span></label>
                                        <input type="date" name="data_inizio" id="data_inizio" class="form-control" required value="{{ old('data_inizio', $sessione->data_inizio) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="data_fine" class="form-label fw-bold">Data Fine <span class="text-danger">*</span></label>
                                        <input type="date" name="data_fine" id="data_fine" class="form-control" required value="{{ old('data_fine', $sessione->data_fine) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="scadenza_inserimento" class="form-label fw-bold">Scadenza inserimento appelli</label>
                                    <input type="date" name="scadenza_inserimento" id="scadenza_inserimento" class="form-control" value="{{ old('scadenza_inserimento', $sessione->scadenza_inserimento) }}">
                                    <div class="form-text small">Facoltativa. Ultimo giorno in cui i docenti possono inserire gli appelli; se vuota viene impostata a un mese prima dell'inizio della sessione.</div>
                                </div>

                                <x-submit-button icon="pencil-square" class="mt-2">Aggiorna Sessione</x-submit-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collegamento allo script esterno -->
        <script src="{{ asset('js/editSessione.js') }}"></script>
        <script src="{{ asset('js/selezioneCorsiSessione.js') }}"></script>
    </body>
</html>