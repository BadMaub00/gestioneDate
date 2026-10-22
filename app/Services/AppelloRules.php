<?php

namespace App\Services;

use App\Models\Appello;
use App\Models\InsegnamentiCorso;
use App\Models\Sessione;
use Carbon\Carbon;

/**
 * Regole di business sugli appelli, condivise tra docente e amministratore,
 * in modo che i due lati non possano più divergere.
 */
class AppelloRules
{
    /** Regole di validazione di data e orari (identiche per docente e admin). */
    public static function regole(Sessione $sessione): array
    {
        return [
            'data' => [
                'required',
                'date',
                'after_or_equal:' . $sessione->data_inizio,
                'before_or_equal:' . $sessione->data_fine,
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Gli appelli d\'esame possono essere programmati solo dal lunedì al venerdì.');
                    }
                },
            ],
            'ora_inizio' => 'required|date_format:H:i|after_or_equal:08:00|before_or_equal:18:00',
            'ora_fine'   => 'required|date_format:H:i|after:ora_inizio|before_or_equal:19:00',
        ];
    }

    public static function messaggi(Sessione $sessione): array
    {
        return [
            'data.after_or_equal'       => "La data dell'appello deve rientrare nella sessione (Inizio: " . Carbon::parse($sessione->data_inizio)->format('d/m/Y') . ").",
            'data.before_or_equal'      => "La data dell'appello deve rientrare nella sessione (Fine: " . Carbon::parse($sessione->data_fine)->format('d/m/Y') . ").",
            'ora_inizio.after_or_equal' => "L'orario di inizio non può essere antecedente le 08:00.",
            'ora_inizio.before_or_equal'=> "L'orario di inizio non può essere successivo alle 18:00.",
            'ora_fine.before_or_equal'  => "L'orario di fine deve essere entro le 19:00.",
            'ora_fine.after'            => "L'orario di fine deve essere successivo all'orario di inizio.",
        ];
    }

    /**
     * Controlla le sovrapposizioni. Restituisce [campo => messaggio] oppure null.
     *  1. stesso insegnamento nello stesso intervallo;
     *  2. stessa aula occupata nello stesso intervallo;
     *  3. insegnamenti dello stesso anno DELLO STESSO corso nello stesso intervallo.
     */
    public static function conflitto(int $insCorsoId, array $d, ?int $escludiAppelloId = null): ?array
    {
        $sovrapposti = function () use ($d, $escludiAppelloId) {
            return Appello::where('data', $d['data'])
                ->where('ora_inizio', '<', $d['ora_fine'])
                ->where('ora_fine', '>', $d['ora_inizio'])
                ->when($escludiAppelloId, fn ($q) => $q->where('appello_id', '!=', $escludiAppelloId));
        };

        if ($sovrapposti()->where('ins_corso_id', $insCorsoId)->exists()) {
            return ['ora_inizio' => 'Esiste già un appello per questo stesso insegnamento nello stesso intervallo orario!'];
        }

        if ($sovrapposti()->where('aula_id', $d['aula_id'])->exists()) {
            return ['aula_id' => 'L\'aula selezionata è già occupata da un altro appello in questa data e fascia oraria!'];
        }

        $ins = InsegnamentiCorso::find($insCorsoId);
        if ($ins) {
            $stessoAnnoStessoCorso = InsegnamentiCorso::where('corso_id', $ins->corso_id)
                ->where('anno_di_corso', $ins->anno_di_corso)
                ->pluck('ins_corso_id');

            if ($sovrapposti()->whereIn('ins_corso_id', $stessoAnnoStessoCorso)->exists()) {
                return ['data' => 'Esiste già un appello per un insegnamento dello stesso anno di questo corso in questa data e orario!'];
            }
        }

        return null;
    }
}
