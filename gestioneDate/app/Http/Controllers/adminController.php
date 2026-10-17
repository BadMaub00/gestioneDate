<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\Corso;
use App\Models\Utente;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\Insegnamento;
use Carbon\Carbon;

class adminController extends Controller
{
    public function dashboard(){
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        return view('admin.dashboard', compact('sessioni'));
    }

    public function indexCorsi() {
        $corsi = Corso::orderBy('nome', 'asc')->get();
        return view('admin.corsi.index', compact('corsi'));
    }

    public function viewPianoDiStudi($corso_id) {
        $corso = Corso::with(['insegnamenti' => function($query) {
            $query->orderBy('anno_di_corso', 'asc');
        }])->findOrFail($corso_id);

        $insegnamentiPerAnno = $corso->insegnamenti->groupBy('anno_di_corso');

        $durataAnni = match ($corso->tipo_corso) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };

        return view('admin.corsi.piano_di_studi', compact('corso', 'insegnamentiPerAnno', 'durataAnni'));
    }

    public function indexPersonale() {
        $personale = Utente::select('nome', 'cognome', 'ruolo')
            ->orderBy('cognome', 'asc')
            ->orderBy('nome', 'asc')
            ->get();

        return view('admin.personale.index', compact('personale'));
    }

    public function createPersonale() {
        return view('admin.personale.create');
    }

    public function storePersonale(Request $request) {
        $request->validate([
            'username'     => 'required|string|max:100|unique:utente,username',
            'password'     => 'required|string|min:6',
            'nome'         => 'required|string|max:100',
            'cognome'      => 'required|string|max:100',
            'data_nascita' => 'required|date',
            'ruolo'        => 'required|in:Docente,Amministratore',
        ]);

        Utente::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'nome'         => $request->nome,
            'cognome'      => $request->cognome,
            'data_nascita' => $request->data_nascita,
            'ruolo'        => $request->ruolo,
        ]);

        return redirect()->route('admin.personale.index')
            ->with('success', 'Membro del personale creato con successo!');
    }

    public function createAssociazioneDocente() {
        $docenti = Utente::where('ruolo', 'Docente')
            ->orderBy('cognome', 'asc')
            ->orderBy('nome', 'asc')
            ->get();

        $insegnamentiCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])
            ->orderBy('corso_id', 'asc')
            ->get();

        return view('admin.personale.associa', compact('docenti', 'insegnamentiCorso'));
    }

    public function indexAssociazioni() {
        $associazioni = DocenteInsegnamento::with([
            'docente', 
            'insegnamentiCorso.insegnamento', 
            'insegnamentiCorso.corso'
        ])->get();

        return view('admin.personale.associazioni_index', compact('associazioni'));
    }

    public function destroyAssociazioneDocente($id) {
        $associazione = DocenteInsegnamento::findOrFail($id);
        $associazione->delete();

        return redirect()->back()->with('success', 'Associazione rimossa con successo.');
    }

    public function storeAssociazioneDocente(Request $request) {
        $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'utente_id'    => 'required|exists:utente,utente_id',
            'ruolo'        => 'required|in:Primario,Secondario',
        ]);

        DocenteInsegnamento::create([
            'ins_corso_id' => $request->ins_corso_id,
            'utente_id'    => $request->utente_id,
            'ruolo'        => $request->ruolo,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Docente associato all\'insegnamento con successo!');
    }

    public function editAppello($id) {
        $appello = Appello::with(['insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso'])->findOrFail($id);
        $sessioni = Sessione::all();

        return view('admin.appelli.edit', compact('appello', 'sessioni'));
    }

    // Salva le modifiche all'appello garantendo i controlli di sicurezza
    public function updateAppello(Request $request, $id) {
        $appello = Appello::findOrFail($id);

        // 1. Validazione base degli orari (08:00 - 19:00 e ora_fine > ora_inizio)
        $validatedData = $request->validate([
            'data'       => 'required|date',
            'ora_inizio' => 'required|date_format:H:i|after_or_equal:08:00|before_or_equal:18:00',
            'ora_fine'   => 'required|date_format:H:i|after:ora_inizio|before_or_equal:19:00',
        ]);

        $sessione = Sessione::findOrFail($appello->sessione_id);

        // 2. Controllo che la data ricada nei limiti della sessione d'esame
        if ($validatedData['data'] < $sessione->data_inizio || $validatedData['data'] > $sessione->data_fine) {
            return back()->withErrors([
                'data' => 'La data dell\'appello deve essere compresa tra l\'inizio (' . Carbon::parse($sessione->data_inizio)->format('d/m/Y') . ') e la fine (' . Carbon::parse($sessione->data_fine)->format('d/m/Y') . ') della sessione.'
            ])->withInput();
        }

        // 3. Controllo sovrapposizione per lo STESSO insegnamento nello stesso slot (escludendo l'appello corrente)
        $stessoInsegnamentoSovrapposto = Appello::where('ins_corso_id', $appello->ins_corso_id)
            ->where('appello_id', '!=', $id)
            ->where('data', $validatedData['data'])
            ->where('ora_inizio', '<', $validatedData['ora_fine'])
            ->where('ora_fine', '>', $validatedData['ora_inizio'])
            ->exists();

        if ($stessoInsegnamentoSovrapposto) {
            return back()->withErrors(['ora_inizio' => 'Esiste già un appello per questo stesso insegnamento nello stesso intervallo orario!'])->withInput();
        }

        // 4. Controllo sovrapposizione oraria per insegnamenti dello STESSO ANNO di corso (escludendo l'appello corrente)
        $annoCorso = InsegnamentiCorso::where('ins_corso_id', $appello->ins_corso_id)->value('anno_di_corso');
        $insSameAge = InsegnamentiCorso::where('anno_di_corso', $annoCorso)->pluck('ins_corso_id')->toArray();

        $orarioNonDisponibile = Appello::where('data', $validatedData['data'])
            ->where('appello_id', '!=', $id)
            ->whereIn('ins_corso_id', $insSameAge)
            ->where('ora_inizio', '<', $validatedData['ora_fine'])
            ->where('ora_fine', '>', $validatedData['ora_inizio'])
            ->exists();

        if ($orarioNonDisponibile) {
            return back()->withErrors(['data' => 'Esiste già un appello per un insegnamento dello stesso anno di corso in questa data e orario!'])->withInput();
        }

        // Aggiornamento dati
        $appello->update($validatedData);

        return redirect()->route('admin.calendario', $appello->sessione_id)
            ->with('success', 'Appello modificato con successo.');
    }

    public function destroyAppello($id) {
        $appello = Appello::findOrFail($id);
        $sessione_id = $appello->sessione_id;
        $appello->delete();

        return redirect()->route('admin.calendario', $sessione_id)
            ->with('success', 'Appello eliminato con successo.');
    }

    public function indexInsegnamenti() {
        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();

        return view('admin.insegnamenti.index', compact('insegnamenti'));
    }

    public function createInsegnamento() {
        return view('admin.insegnamenti.create');
    }

    public function storeInsegnamento(Request $request) {
        $request->validate([
            'nome' => 'required|string|max:150',
        ]);

        Insegnamento::create([
            'nome' => $request->nome,
        ]);

        return redirect()->route('admin.insegnamenti.index')
            ->with('success', 'Insegnamento creato con successo!');
    }

    public function destroyInsegnamento($insegnamento_id) {
        $insegnamento = Insegnamento::findOrFail($insegnamento_id);
        $insegnamento->delete();

        return redirect()->back()->with('success', 'Insegnamento eliminato con successo.');
    }

    public function createCorso() {
        return view('admin.corsi.create');
    }

    public function storeCorso(Request $request) {
        $request->validate([
            'nome'       => 'required|string|max:150',
            'tipo_corso' => 'required|in:Triennale,Magistrale,Magistrale a ciclo unico',
        ]);

        Corso::create([
            'nome'       => $request->nome,
            'tipo_corso' => $request->tipo_corso,
        ]);

        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea creato con successo!');
    }

    public function createInsegnamentoCorso($corso_id) {
        $corso = Corso::findOrFail($corso_id);
        
        $durataAnni = match ($corso->tipo_corso) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };

        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();

        return view('admin.corsi.createInsCorso', compact('corso', 'durataAnni', 'insegnamenti'));
    }

    public function storeInsegnamentoCorso(Request $request, $corso_id) {
        $request->validate([
            'insegnamento_id' => 'required|exists:insegnamento,insegnamento_id',
            'anno_di_corso'   => 'required|integer|min:1|max:5',
            'cfu'             => 'required|integer|min:1|max:30',
            'durata'          => 'required|integer|min:1',
        ]);

        InsegnamentiCorso::create([
            'corso_id'        => $corso_id,
            'insegnamento_id' => $request->insegnamento_id,
            'anno_di_corso'   => $request->anno_di_corso,
            'cfu'             => $request->cfu,
            'durata'          => $request->durata,
        ]);

        return redirect()->route('admin.corsi.pianoDiStudi', $corso_id)
            ->with('success', 'Insegnamento aggiunto al piano di studi con successo!');
    }

    public function viewcalendario_generale($sessione_id = null){
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        if (!$sessione_id && $sessioni->isNotEmpty()) {
            $sessione_id = $sessioni->first()->getKey(); 
        }

        $appelli = Appello::where('sessione_id', $sessione_id)->with(['insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso'])
            ->orderBy('data', 'asc')
            ->orderBy('ora_inizio', 'asc')
            ->get();

        return view('admin.calendario', compact('appelli', 'sessioni', 'sessione_id'));
    }
}