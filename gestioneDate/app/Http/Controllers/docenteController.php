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
    public function dashboard(){
        $user = Auth::user();

        // Recupera gli insegnamenti assegnati a questo docente
        //$insegnamentiCorsi = InsegnamentiCorso::where('user_id', $user->getUtenteId())->with('insegnamento')->toArray(); //insegnamento è il nome del metodo in InsegnamentiCorso che definisce la relazione con il modello Insegnamento
        $insegnamentiCorsi = $this->insAssociati($user);
        //$insegnamentiCorsi->firstWhere("ins_corso_id", 3)->insegnamento->nome;

        /*foreach ($insegnamentiCorsi as $insC) {
            $insC->insegnamento_nome = $insC->insegnamento->nome;
        }
        va usato nella view per restiruire il nome dell'insegnamento associato a ciascun insegnamento del corso.*/

        return view('docente.dashboard', compact('insegnamentiCorsi'));
    }

    public function selezionaSessione()
    {
        // Recupera tutte le sessioni d'esame disponibili, ordinate per data
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        return view('docente.selezionaSessione', compact('sessioni'));
    }

    // Elabora la scelta dal form e reindirizza alla vista calendario con l'id scelto
    public function procediCalendarioPersonale(Request $request)
    {
        $request->validate([
            'sessione_id' => 'required|exists:sessione_esami,sessione_id',
        ]);

        return redirect()->route('docente.calendario', ['sessione_id' => $request->sessione_id]);
    }

    // Vista del Calendario Personale (rimane pulita e con parametro obbligatorio)
    public function viewCalendarioPersonale(int $sessione_id)
    {
        /** @var \App\Models\Utente $user */
        $user = Auth::user();

        // 1. Recupera i dati della sessione selezionata (es. nome e anno)
        $sessione = Sessione::findOrFail($sessione_id);

        // 2. Recupera gli insegnamenti associati al docente
        $insegnamentiCorsi = $this->insAssociati($user);

        // 3. Carica gli appelli filtrati per questa sessione
        $insegnamentiCorsi->load(['appelli' => function($query) use ($sessione_id) {
            $query->where('sessione_id', $sessione_id)
                ->orderBy('data')
                ->orderBy('ora_inizio');
        }]);

        return view('docente.calendario_personale', compact('insegnamentiCorsi', 'sessione'));
    }
    public function selezionaFiltriGenerale()
    {
        /** @var \App\Models\Utente $user */
        $user = Auth::user();

        // Recupera solo i corsi in cui il docente loggato insegna
        $insegnamentiCorsi = $this->insAssociati($user);
        $corsi = $insegnamentiCorsi->pluck('corso')->unique('corso_id')->values();

        // Recupera tutte le sessioni d'esame
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        return view('docente.selezionaFiltriGenerale', compact('corsi', 'sessioni'));
    }

    // Endpoint AJAX per recuperare gli anni di corso in cui il docente insegna per il corso scelto
    public function getAnniCorsoPerDocente(int $corso_id)
    {
        /** @var \App\Models\Utente $user */
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

    // Elabora il form e reindirizza a calendario_generale
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
        $appelliAnnoCorso = Appello::join('insegnamento_corso', 'appello.ins_corso_id', '=', 'insegnamento_corso.ins_corso_id')
            ->join('insegnamento', 'insegnamento_corso.insegnamento_id', '=', 'insegnamento.insegnamento_id') 
            ->join('corso', 'insegnamento_corso.corso_id', '=', 'corso.corso_id') 
            ->join('sessione_esami', 'appello.sessione_id', '=', 'sessione_esami.sessione_id')
            ->leftJoin('docente_insegnamento', 'insegnamento_corso.ins_corso_id', '=', 'docente_insegnamento.ins_corso_id')
            ->leftJoin('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->where([
                'insegnamento_corso.corso_id' => $corso_id,
                'insegnamento_corso.anno_di_corso' => $annoCorso,
                'appello.sessione_id' => $sessione_id,
            ])
            ->orderBy('appello.data')
            ->orderBy('appello.ora_inizio')
            ->select(
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

        // Recupera i dettagli di Corso e Sessione per l'intestazione
        $corso = \App\Models\Corso::find($corso_id);
        $sessione = \App\Models\Sessione::find($sessione_id);

        return view('docente.calendario_generale', compact(
            'appelliAnnoCorso', 
            'sessione_id', 
            'annoCorso', 
            'corso_id',
            'corso',
            'sessione'
        ));
    }

    private function insAssociati(Utente $user)
    {
        $insegnamentiTemp = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->pluck('ins_corso_id')
            ->toArray();

        return InsegnamentiCorso::whereIn('ins_corso_id', $insegnamentiTemp)
            ->with(['insegnamento', 'corso'])
            ->get()
            ->sortBy([
                ['corso.nome', 'asc'],
                ['insegnamento.nome', 'asc'],
            ])
            ->values(); // Ripristina le chiavi numeriche dell'array
    }

    public function index()
    {
        // Recupera tutte le sessioni dal database
        $sessioni = Sessione::all(); 

        return view('docente.dashboard', compact('sessioni'));
    }
    
}
