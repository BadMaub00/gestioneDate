<?php

namespace App\Services;

use App\Models\Corso;
use App\Models\Sessione;
use Carbon\Carbon;

/**
 * Regole di business sulle sessioni d'esame, usate dall'amministratore
 * (creazione e modifica): anticipo minimo, durata minima, numero di appelli,
 * distanza tra sessioni dello stesso corso e vincoli sugli appelli già inseriti.
 */
class SessioneRules
{
    /** Regole comuni a creazione e modifica (data_inizio viene aggiunta dal chiamante). */
    public static function regole(): array
    {
        return [
            'nome'           => 'required|string|max:150',
            'anno'           => 'required|integer',
            'numero_appelli' => 'required|integer|min:1|max:4',
            'corsi'          => 'required|array|min:1',
            'corsi.*'        => 'integer|distinct|exists:corso,corso_id',
            'scadenza_inserimento' => ['nullable', 'date', 'before_or_equal:data_inizio'],
            'data_fine'      => ['required', 'date', 'after_or_equal:data_inizio', function ($attribute, $value, $fail) {
                $inizio = request('data_inizio');

                if ($inizio && Carbon::parse($value)->lt(Carbon::parse($inizio)->addWeeks(2))) {
                    $fail('La data di fine deve essere di almeno due settimane successiva alla data di inizio.');
                }
            }],
        ];
    }

    /** Messaggi di errore delle regole comuni. */
    public static function messaggi(): array
    {
        return [
            'numero_appelli.min' => 'Il numero minimo di appelli previsti è 1.',
            'numero_appelli.max' => 'Il numero massimo di appelli previsti per una sessione è 4.',
            'scadenza_inserimento.after_or_equal'  => 'La scadenza per l\'inserimento degli appelli non può essere nel passato.',
            'scadenza_inserimento.before_or_equal' => 'La scadenza per l\'inserimento degli appelli non può essere successiva alla data di inizio della sessione.',
            'corsi.required'     => 'Seleziona almeno un corso di studi.',
            'corsi.min'          => 'Seleziona almeno un corso di studi.',
            'corsi.*.exists'     => 'Uno dei corsi selezionati non esiste.',
        ];
    }

    /** Regole per la creazione: la sessione deve iniziare con almeno un mese di anticipo. */
    public static function regoleCreazione(): array
    {
        $minDataInizio = Carbon::today()->addMonth()->format('Y-m-d');

        return array_merge(self::regole(), [
            'data_inizio'          => ['required', 'date', 'after_or_equal:' . $minDataInizio],
            'scadenza_inserimento' => ['nullable', 'date', 'after_or_equal:today', 'before_or_equal:data_inizio'],
        ]);
    }

    public static function messaggiCreazione(): array
    {
        return self::messaggi() + [
            'data_inizio.after_or_equal' => 'La data di inizio della sessione deve essere programmata con almeno un mese di anticipo rispetto a oggi.',
        ];
    }

    /** Regole per la modifica (nessun vincolo di anticipo sulla data di inizio). */
    public static function regoleModifica(): array
    {
        return self::regole() + ['data_inizio' => 'required|date'];
    }

    public static function messaggiModifica(): array
    {
        return self::messaggi();
    }

    /**
     * Due sessioni dello stesso corso devono distare almeno un mese
     * (tra la fine dell'una e l'inizio dell'altra); le sovrapposizioni sono quindi escluse.
     * Restituisce un array di errori ['corsi' => messaggio] oppure [].
     *
     * @param int[] $corsiIds
     * @param int[] $escludiSessioneIds sessioni da ignorare (quelle che si stanno modificando)
     */
    public static function conflittiDistanza(array $corsiIds, string $inizio, string $fine, array $escludiSessioneIds = []): array
    {
        $messaggi = [];

        foreach ($corsiIds as $corsoId) {
            $vicina = Sessione::vicinaA($corsoId, $inizio, $fine, $escludiSessioneIds);

            if ($vicina) {
                $messaggi[] = "Il corso '" . self::nomeCorso($corsoId) . "' ha già la sessione '{$vicina->nome}' (dal "
                    . Carbon::parse($vicina->data_inizio)->format('d/m/Y') . " al "
                    . Carbon::parse($vicina->data_fine)->format('d/m/Y')
                    . "): tra due sessioni dello stesso corso deve passare almeno un mese.";
            }
        }

        return $messaggi ? ['corsi' => implode(' ', $messaggi)] : [];
    }

    /**
     * Vincoli sulla modifica di una sessione rispetto agli appelli già inseriti:
     *  - un corso può essere rimosso solo se non ha appelli;
     *  - le nuove date devono contenere tutti gli appelli esistenti;
     *  - numero_appelli non può essere inferiore a quello già usato da un insegnamento.
     *
     * @param iterable $daAggiornare sessioni che restano, indicizzate per corso_id
     * @param iterable $daRimuovere  sessioni dei corsi deselezionati, indicizzate per corso_id
     * @param array    $validated    dati già validati (data_inizio, data_fine, numero_appelli)
     * @return array errori nel formato ['campo' => messaggio], vuoto se tutto è valido
     */
    public static function vincoliModifica(iterable $daAggiornare, iterable $daRimuovere, array $validated): array
    {
        $errori = [];

        // Sessioni da rimuovere: non devono avere appelli
        foreach ($daRimuovere as $corsoId => $s) {
            if ($s->appelli()->exists()) {
                $errori['corsi'] = "Il corso '" . self::nomeCorso($corsoId) . "' ha già appelli in questa sessione: eliminali prima di rimuoverlo.";
                break;
            }
        }

        // Sessioni da aggiornare: appelli dentro il nuovo periodo e numero_appelli sufficiente
        foreach ($daAggiornare as $corsoId => $s) {
            $fuoriPeriodo = $s->appelliFuoriPeriodo($validated['data_inizio'], $validated['data_fine']);

            if ($fuoriPeriodo > 0) {
                $errori['data_inizio'] = "Per il corso '" . self::nomeCorso($corsoId) . "' ci sono {$fuoriPeriodo} appelli già inseriti fuori dal nuovo periodo: spostali o eliminali prima di modificare le date.";
                break;
            }
        }

        foreach ($daAggiornare as $corsoId => $s) {
            $massimoPresente = $s->maxAppelliPerInsegnamento();

            if ($validated['numero_appelli'] < $massimoPresente) {
                $errori['numero_appelli'] = "Per il corso '" . self::nomeCorso($corsoId) . "' esiste già un insegnamento con {$massimoPresente} appelli in questa sessione: il numero previsto non può essere inferiore.";
                break;
            }
        }

        return $errori;
    }

    /** Etichetta del corso per i messaggi di errore. */
    public static function nomeCorso($corsoId): string
    {
        return Corso::find($corsoId)?->etichetta() ?? "#{$corsoId}";
    }
}