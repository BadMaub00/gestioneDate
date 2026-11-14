<?php

namespace App\Services;

use App\Models\Appello;
use App\Models\DocenteInsegnamento;
use App\Models\Sessione;
use App\Models\Utente;
use Illuminate\Support\Collection;

/** Costruisce i dati dei calendari visti dal docente (personale e generale). */
class CalendarioDocente
{
    public function __construct(private InsegnamentiDocente $insegnamenti)
    {
    }

    /** Insegnamenti del docente nel corso della sessione, ognuno con i propri appelli della sessione. */
    public function personale(Utente $docente, Sessione $sessione)
    {
        // La sessione appartiene a un solo corso: solo gli insegnamenti del docente in quel corso
        $insegnamentiCorsi = $this->insegnamenti->associati($docente)
            ->filter(fn ($ic) => $sessione->appartieneACorso((int) $ic->corso_id))
            ->values();

        $insegnamentiCorsi->load(['appelli' => function ($query) use ($sessione) {
            $query->where('sessione_id', $sessione->sessione_id)
                ->with('aula.edificio')
                ->orderBy('data')
                ->orderBy('ora_inizio');
        }]);

        return $insegnamentiCorsi;
    }

    /**
     * Appelli di corso, anno e sessione in ordine cronologico, con visibilità differenziata:
     * dettagli solo per i propri appelli, "Occupato" per gli altri.
     */
    public function generale(Utente $docente, Sessione $sessione, int $corsoId, int $annoCorso): Collection
    {
        $appelliRaw = Appello::where('sessione_id', $sessione->sessione_id)
            ->delCorso($corsoId, $annoCorso)
            ->with(['insegnamentoCorso.insegnamento', 'aula.edificio'])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        // Due sole query invece di una per ogni appello: insegnamenti del docente loggato...
        $insCorsoIds = $appelliRaw->pluck('ins_corso_id')->unique()->values();

        $mieiInsegnamenti = DocenteInsegnamento::delDocente($docente->getKey())
            ->whereIn('ins_corso_id', $insCorsoIds)
            ->pluck('ins_corso_id')
            ->flip();

        // ...e docente di riferimento di ciascuno (il Primario, se presente)
        $docentiRiferimento = DocenteInsegnamento::whereIn('docente_insegnamento.ins_corso_id', $mieiInsegnamenti->keys())
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->orderByRaw("docente_insegnamento.ruolo = '" . DocenteInsegnamento::RUOLO_PRIMARIO . "' DESC")
            ->orderBy('docente_insegnamento.doc_ins_id')
            ->select('docente_insegnamento.ins_corso_id', 'utente.nome', 'utente.cognome')
            ->get()
            ->unique('ins_corso_id')
            ->keyBy('ins_corso_id');

        return $appelliRaw->map(function ($appello) use ($mieiInsegnamenti, $docentiRiferimento) {
            $isMio = $mieiInsegnamenti->has($appello->ins_corso_id);
            $docenteObj = $isMio ? $docentiRiferimento->get($appello->ins_corso_id) : null;

            return (object) [
                'appello_id'        => $appello->appello_id,
                'data'              => $appello->data,
                'ora_inizio'        => $appello->ora_inizio,
                'ora_fine'          => $appello->ora_fine,
                'is_mio_appello'    => $isMio,
                'nome_insegnamento' => $isMio ? ($appello->insegnamentoCorso->insegnamento->nome ?? 'Insegnamento') : 'Occupato',
                'docente_nome'      => $isMio ? ($docenteObj->nome ?? '') : '',
                'docente_cognome'   => $isMio ? ($docenteObj->cognome ?? '') : '',
                'nome_aula'         => $isMio ? ($appello->aula->nome ?? 'N/D') : 'Occupata',
                'nome_edificio'     => $isMio ? ($appello->aula->edificio->nome ?? 'N/D') : '',
            ];
        });
    }
}
