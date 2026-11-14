<?php

namespace App\Services;

use App\Models\Appello;
use Illuminate\Support\Collection;

/** Dati del calendario generale degli appelli visto dall'amministratore. */
class CalendarioAdmin
{
    /**
     * Rende coerenti i filtri: se c'è solo la sessione, il corso è quello della sessione;
     * se sessione e corso non corrispondono (o la sessione non esiste), la sessione viene ignorata.
     *
     * @return array{0: mixed, 1: mixed} [sessione_id, corso_id]
     */
    public function normalizzaFiltri($sessioneId, $corsoId, Collection $sessioni): array
    {
        if ($sessioneId) {
            $sessioneScelta = $sessioni->firstWhere('sessione_id', (int) $sessioneId);

            if (!$sessioneScelta) {
                $sessioneId = null;
            } elseif (!$corsoId) {
                $corsoId = $sessioneScelta->corso_id;
            } elseif ((int) $sessioneScelta->corso_id !== (int) $corsoId) {
                $sessioneId = null;
            }
        }

        return [$sessioneId, $corsoId];
    }

    /** Appelli filtrati, ordinati e raggruppati: Sessione ID -> Nome Corso -> Anno di Corso. */
    public function appelliRaggruppati($sessioneId, $corsoId, $annoDiCorso): Collection
    {
        $query = Appello::with(['sessione.corso', 'insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso', 'aula.edificio']);

        if ($sessioneId) {
            $query->where('sessione_id', $sessioneId);
        }

        if ($corsoId) {
            $query->delCorso($corsoId, $annoDiCorso);
        }

        // Ordinamento: sessione, corso (nome), anno di corso, insegnamento (nome), data e ora
        $appelli = $query->get()->sortBy([
            fn ($a, $b) => strcmp($a->sessione->data_inizio ?? '', $b->sessione->data_inizio ?? ''),
            fn ($a, $b) => strcmp($a->insegnamentoCorso->corso->nome ?? '', $b->insegnamentoCorso->corso->nome ?? ''),
            fn ($a, $b) => ($a->insegnamentoCorso->anno_di_corso ?? 0) <=> ($b->insegnamentoCorso->anno_di_corso ?? 0),
            fn ($a, $b) => strcmp($a->insegnamentoCorso->insegnamento->nome ?? '', $b->insegnamentoCorso->insegnamento->nome ?? ''),
            fn ($a, $b) => strcmp($a->data . $a->ora_inizio, $b->data . $b->ora_inizio),
        ]);

        return $appelli->groupBy([
            fn ($item) => $item->sessione_id,
            fn ($item) => $item->insegnamentoCorso->corso->nome ?? 'Senza Corso',
            fn ($item) => $item->insegnamentoCorso->anno_di_corso ?? 'Non Specificato',
        ]);
    }
}
