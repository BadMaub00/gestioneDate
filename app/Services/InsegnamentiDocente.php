<?php

namespace App\Services;

use App\Models\DocenteInsegnamento;
use App\Models\InsegnamentiCorso;
use App\Models\Utente;
use Illuminate\Support\Collection;

/**
 * Accesso in sola lettura agli insegnamenti di UN docente.
 * Tutte le query sono vincolate a $docente: i controller docente non fanno mai
 * lookup per id "nudo", così un docente non può vedere dati di un altro.
 */
class InsegnamentiDocente
{
    /** Insegnamenti del docente (con insegnamento e corso), ordinati per corso e nome; ruolo in ->ruolo_docente. */
    public function associati(Utente $docente): Collection
    {
        return DocenteInsegnamento::where('utente_id', $docente->getKey())
            ->with(['insegnamentiCorso.insegnamento', 'insegnamentiCorso.corso'])
            ->get()
            ->map(function ($docIns) {
                $insCorso = $docIns->insegnamentiCorso;
                if ($insCorso) {
                    $insCorso->ruolo_docente = $docIns->ruolo;
                }

                return $insCorso;
            })
            ->filter()
            ->sortBy(fn ($ic) => ($ic->corso->nome ?? '') . ' ' . ($ic->insegnamento->nome ?? ''))
            ->values();
    }

    /** Un insegnamento del docente, oppure 404 se non gli appartiene. */
    public function trovaOppure404(Utente $docente, int $insCorsoId): InsegnamentiCorso
    {
        $ic = $this->associati($docente)->firstWhere('ins_corso_id', $insCorsoId);
        abort_unless($ic, 404, 'Insegnamento non trovato o non associato a questo docente.');

        return $ic;
    }

    /** Corsi in cui il docente insegna, in ordine alfabetico. */
    public function corsi(Utente $docente): Collection
    {
        return $this->associati($docente)->pluck('corso')->filter()->unique('corso_id')->sortBy('nome')->values();
    }

    public function haCorso(Utente $docente, int $corsoId): bool
    {
        return InsegnamentiCorso::where('corso_id', $corsoId)
            ->whereIn('ins_corso_id', DocenteInsegnamento::where('utente_id', $docente->getKey())->select('ins_corso_id'))
            ->exists();
    }

    /** Associazione (con ruolo) tra il docente e l'insegnamento, se esiste. */
    public function incarico(Utente $docente, int $insCorsoId): ?DocenteInsegnamento
    {
        return DocenteInsegnamento::where('ins_corso_id', $insCorsoId)
            ->where('utente_id', $docente->getKey())
            ->first();
    }
}
