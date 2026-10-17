<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\Utente;
use App\Models\Sessione;

class docenteController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $insegnamentiCorsi = $this->insAssociati($user);

        return view('docente.dashboard', compact('insegnamentiCorsi'));
    }

    public function selezionaSessione()
    {
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        return view('docente.selezionaSessione', compact('sessioni'));
    }

    public function procediCalendarioPersonale(Request $request)
    {
        $request->validate([
            'sessione_id' => 'required|exists:sessione_esami,sessione_id',
        ]);

        return redirect()->route('docente.calendario', ['sessione_id' => $request->sessione_id]);
    }

    public function viewCalendarioPersonale(int $sessione_id)
    {
        /** @var Utente $user */
        $user = Auth::user();

        $sessione = Sessione::findOrFail($sessione_id);
        $insegnamentiCorsi = $this->insAssociati($user);

        $insegnamentiCorsi->load(['appelli' => function($query) use ($sessione_id) {
            $query->where('sessione_id', $sessione_id)
                ->orderBy('data')
                ->orderBy('ora_inizio');
        }]);

        return view('docente.calendario_personale', compact('insegnamentiCorsi', 'sessione'));
    }

    public function selezionaFiltriGenerale()
    {
        /** @var Utente $user */
        $user = Auth::user();

        $insegnamentiCorsi = $this->insAssociati($user);
        $corsi = $insegnamentiCorsi->pluck('corso')->unique('corso_id')->values();
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        return view('docente.selezionaFiltriGenerale', compact('corsi', 'sessioni'));
    }

    public function getAnniCorsoPerDocente(int $corso_id)
    {
        /** @var Utente $user */
        $user = Auth::user();

        $insCorsoIds = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->pluck('ins_corso_id')
            ->toArray();

        $anni = InsegnamentiCorso::whereIn('ins_corso_id', $insCorsoIds)
            ->where('corso_id', $corso_id)
            ->distinct()
            ->orderBy('anno_di_corso', 'asc')
            ->pluck('anno_di_corso');

        return response()->json($anni);
    }

    public function procedicalendario_generale(Request $request)
    {
        $request->validate([
            'corso_id' => 'required|exists:corso,corso_id',
            'annoCorso' => 'required|integer|min:1',
            'sessione_id' => 'required|exists:sessione_esami,sessione_id',
        ]);

        return redirect()->route('docente.calendario.generale', [
            'sessione_id' => $request->sessione_id,
            'annoCorso' => $request->annoCorso,
            'corso_id' => $request->corso_id,
        ]);
    }

    public function viewcalendario_generale(int $sessione_id, int $annoCorso, int $corso_id)
    {
        /** @var Utente $user */
        $user = Auth::user();

        $mieiInsCorsoIds = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->pluck('ins_corso_id')
            ->toArray();

        $appelliAnnoCorso = Appello::join('insegnamento_corso', 'appello.ins_corso_id', '=', 'insegnamento_corso.ins_corso_id')
            ->join('insegnamento', 'insegnamento_corso.insegnamento_id', '=', 'insegnamento.insegnamento_id') 
            ->join('corso', 'insegnamento_corso.corso_id', '=', 'corso.corso_id') 
            ->join('sessione_esami', 'appello.sessione_id', '=', 'sessione_esami.sessione_id')
            ->leftJoin('docente_insegnamento', function($join) {
                $join->on('insegnamento_corso.ins_corso_id', '=', 'docente_insegnamento.ins_corso_id')
                    ->where('docente_insegnamento.ruolo', '=', 'Primario');
            })
            ->leftJoin('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->where([
                'insegnamento_corso.corso_id' => $corso_id,
                'insegnamento_corso.anno_di_corso' => $annoCorso,
                'appello.sessione_id' => $sessione_id,
            ])
            ->orderBy('appello.data')
            ->orderBy('appello.ora_inizio')
            ->select(
                'appello.appello_id',
                'appello.data', 
                'appello.ora_inizio', 
                'appello.ora_fine', 
                'insegnamento_corso.ins_corso_id',
                'insegnamento.nome as nome_insegnamento', 
                'corso.nome as nome_corso',
                'sessione_esami.nome as nome_sessione', 
                'sessione_esami.anno as anno_sessione',
                'utente.nome as docente_nome',
                'utente.cognome as docente_cognome'
            )
            ->get();

        $appelliAnnoCorso->transform(function ($appello) use ($mieiInsCorsoIds) {
            $appello->setAttribute('is_mio_appello', in_array($appello->ins_corso_id, $mieiInsCorsoIds));
            return $appello;
        });

        $corso = \App\Models\Corso::find($corso_id);
        $sessione = Sessione::find($sessione_id);

        return view('docente.calendario_generale', compact(
            'appelliAnnoCorso', 
            'sessione_id', 
            'annoCorso', 
            'corso_id',
            'corso',
            'sessione'
        ));
    }

    public function dettaglioInsegnamento(int $ins_corso_id)
    {
        /** @var Utente $user */
        $user = Auth::user();

        // 1. Cerca l'insegnamento all'interno della collection restituita da insAssociati()
        $insegnamentoCorso = $this->insAssociati($user)->firstWhere('ins_corso_id', $ins_corso_id);

        // Se l'ID non è presente tra le materie del docente, blocca l'accesso
        if (!$insegnamentoCorso) {
            abort(404, 'Insegnamento non trovato o non associato a questo docente.');
        }

        // 2. Recupera il ruolo per questo specifico insegnamento per il docente loggato
        $docenteInsegnamento = DocenteInsegnamento::where('ins_corso_id', $ins_corso_id)
            ->where('utente_id', $user->getKey())
            ->first();

        // 3. Recupera il docente primario esplicitando le colonne e la tabella per evitare ambiguità SQL
        $docentePrimario = DocenteInsegnamento::where('docente_insegnamento.ins_corso_id', $ins_corso_id)
            ->where('docente_insegnamento.ruolo', 'Primario')
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->select('utente.utente_id', 'utente.nome', 'utente.cognome', 'docente_insegnamento.ruolo')
            ->first();

        // 4. Recupera i docenti secondari esplicitando le colonne e la tabella per evitare ambiguità SQL
        $docentiSecondari = DocenteInsegnamento::where('docente_insegnamento.ins_corso_id', $ins_corso_id)
            ->where('docente_insegnamento.ruolo', 'Secondario')
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->select('utente.utente_id', 'utente.nome', 'utente.cognome', 'docente_insegnamento.ruolo')
            ->get();

        return view('docente.dettaglio_insegnamento', compact(
            'insegnamentoCorso', 
            'docenteInsegnamento', 
            'docentePrimario', 
            'docentiSecondari'
        ));
    }

    public function gestioneAppelliInsegnamento(int $ins_corso_id)
    {
        /** @var Utente $user */
        $user = Auth::user();

        // 1. Verifica che l'insegnamento appartenga al docente loggato
        $insegnamentoCorso = $this->insAssociati($user)->firstWhere('ins_corso_id', $ins_corso_id);

        if (!$insegnamentoCorso) {
            abort(404, 'Insegnamento non trovato o non associato a questo docente.');
        }

        // 2. Ruolo del docente per questo specifico insegnamento
        $docenteInsegnamento = DocenteInsegnamento::where('ins_corso_id', $ins_corso_id)
            ->where('utente_id', $user->getKey())
            ->first();

        // 3. Recupera tutte le sessioni d'esame in ordine cronologico
        $sessioni = Sessione::orderBy('data_inizio', 'asc')->get();

        // 4. Carica gli appelli per questo specifico insegnamento diviso per ogni sessione
        foreach ($sessioni as $sessione) {
            $sessione->appelli_insegnamento = Appello::where('ins_corso_id', $ins_corso_id)
                ->where('sessione_id', $sessione->sessione_id)
                ->orderBy('data', 'asc')
                ->orderBy('ora_inizio', 'asc')
                ->get();
        }

        return view('docente.appello_form', compact(
            'insegnamentoCorso',
            'docenteInsegnamento',
            'sessioni'
        ));
    }

    private function insAssociati(Utente $user)
    {
        $docenteInsegnamenti = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->with(['insegnamentiCorso.insegnamento', 'insegnamentiCorso.corso'])
            ->get();

        return $docenteInsegnamenti->map(function ($docIns) {
            $insCorso = $docIns->insegnamentiCorso;
            
            if ($insCorso) {
                $insCorso->ruolo_docente = $docIns->ruolo; 
            }
            
            return $insCorso;
        })
        ->filter()
        ->sortBy(function ($insC) {
            return ($insC->corso->nome ?? '') . ' ' . ($insC->insegnamento->nome ?? '');
        })
        ->values();
    }

    public function index()
    {
        $sessioni = Sessione::all(); 

        return view('docente.dashboard', compact('sessioni'));
    }
}