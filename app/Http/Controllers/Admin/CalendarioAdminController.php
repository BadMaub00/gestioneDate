<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appello;
use App\Models\Corso;
use App\Models\Sessione;
use Illuminate\Http\Request;

/** Calendario generale degli appelli visto dall'amministratore. */
class CalendarioAdminController extends Controller
{
    public function index(Request $request)
    {
        $sessione_id = $request->input('sessione_id');
        $corso_id = $request->input('corso_id');
        $anno_di_corso = $request->input('anno_di_corso');

        // Sessioni in ordine di data di inizio (ognuna appartiene a un corso)
        $sessioni = Sessione::orderBy('data_inizio', 'asc')->get();

        // Link o filtri parziali: se c'è solo la sessione, il corso è quello della sessione;
        // se sessione e corso non corrispondono, la sessione viene ignorata
        if ($sessione_id) {
            $sessioneScelta = $sessioni->firstWhere('sessione_id', (int) $sessione_id);

            if (!$sessioneScelta) {
                $sessione_id = null;
            } elseif (!$corso_id) {
                $corso_id = $sessioneScelta->corso_id;
            } elseif ((int) $sessioneScelta->corso_id !== (int) $corso_id) {
                $sessione_id = null;
            }
        }

        $corsi = Corso::orderBy('nome', 'asc')->get();
        foreach ($corsi as $corso) {
            $corso->durata_anni = $corso->durataAnni();
        }

        $query = Appello::with(['sessione.corso', 'insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso', 'aula.edificio']);

        if ($sessione_id) {
            $query->where('sessione_id', $sessione_id);
        }

        if ($corso_id) {
            $query->whereHas('insegnamentoCorso', function ($q) use ($corso_id, $anno_di_corso) {
                $q->where('corso_id', $corso_id);
                if ($anno_di_corso) {
                    $q->where('anno_di_corso', $anno_di_corso);
                }
            });
        }

        // Ordinamento: sessione, corso (nome), anno di corso, insegnamento (nome), data e ora
        $appelli = $query->get()->sortBy([
            fn ($a, $b) => strcmp($a->sessione->data_inizio ?? '', $b->sessione->data_inizio ?? ''),
            fn ($a, $b) => strcmp($a->insegnamentoCorso->corso->nome ?? '', $b->insegnamentoCorso->corso->nome ?? ''),
            fn ($a, $b) => ($a->insegnamentoCorso->anno_di_corso ?? 0) <=> ($b->insegnamentoCorso->anno_di_corso ?? 0),
            fn ($a, $b) => strcmp($a->insegnamentoCorso->insegnamento->nome ?? '', $b->insegnamentoCorso->insegnamento->nome ?? ''),
            fn ($a, $b) => strcmp($a->data . $a->ora_inizio, $b->data . $b->ora_inizio),
        ]);

        // Raggruppamento annidato: Sessione ID -> Nome Corso -> Anno di Corso
        $appelliGrouped = $appelli->groupBy([
            fn ($item) => $item->sessione_id,
            fn ($item) => $item->insegnamentoCorso->corso->nome ?? 'Senza Corso',
            fn ($item) => $item->insegnamentoCorso->anno_di_corso ?? 'Non Specificato',
        ]);

        return view('admin.calendario', compact('appelliGrouped', 'sessioni', 'corsi', 'sessione_id', 'corso_id', 'anno_di_corso'));
    }
}
