<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\Corso;
use App\Models\Utente;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\Insegnamento;

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
        // 1. Recupera il corso con gli insegnamenti ordinati
        $corso = Corso::with(['insegnamenti' => function($query) {
            $query->orderBy('anno_di_corso', 'asc');
        }])->findOrFail($corso_id);

        // 2. Raggruppa gli insegnamenti usando la colonna pivot corretta (anno_di_corso)
        $insegnamentiPerAnno = $corso->insegnamenti->groupBy('anno_di_corso');

        // 3. Calcola la durata del corso in base al tipo_corso
        $durataAnni = match ($corso->tipo_corso) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3, // Triennale
        };

        // 4. Passa tutte le variabili necessarie alla vista
        return view('admin.corsi.piano_di_studi', compact('corso', 'insegnamentiPerAnno', 'durataAnni'));
    }

    public function indexPersonale() {
        // Recupera tutti gli utenti ordinati per cognome e nome
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
            'password'     => Hash::make($request->password), // Cifratura Hash sicura
            'nome'         => $request->nome,
            'cognome'      => $request->cognome,
            'data_nascita' => $request->data_nascita,
            'ruolo'        => $request->ruolo,
        ]);

        return redirect()->route('admin.personale.index')
            ->with('success', 'Membro del personale creato con successo!');
    }

    public function createAssociazioneDocente() {
        // Recupera solo gli utenti con ruolo Docente
        $docenti = Utente::where('ruolo', 'Docente')
            ->orderBy('cognome', 'asc')
            ->orderBy('nome', 'asc')
            ->get();

        // Recupera gli insegnamenti dei corsi con i relativi dettagli
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

    // Elimina un'associazione docente-insegnamento
    public function destroyAssociazioneDocente($id) {
        $associazione = DocenteInsegnamento::findOrFail($id);
        $associazione->delete();

        return redirect()->back()->with('success', 'Associazione rimossa con successo.');
    }

    // Salva l'associazione nel database
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

    // Salva le modifiche all'appello
    public function updateAppello(Request $request, $id) {
        $appello = Appello::findOrFail($id);

        $request->validate([
            'data'       => 'required|date',
            'ora_inizio' => 'required',
            'ora_fine'   => 'required',
        ]);

        $appello->update([
            'data'       => $request->data,
            'ora_inizio' => $request->ora_inizio,
            'ora_fine'   => $request->ora_fine,
        ]);

        return redirect()->route('admin.calendario', $appello->sessione_id)
            ->with('success', 'Appello modificato con successo.');
    }

    // Elimina un appello d'esame
    public function destroyAppello($id) {
        $appello = Appello::findOrFail($id);
        $sessione_id = $appello->sessione_id;
        $appello->delete();

        return redirect()->route('admin.calendario', $sessione_id)
            ->with('success', 'Appello eliminato con successo.');
    }

    public function indexInsegnamenti() {
        // Recupera solo la lista degli insegnamenti ordinati per nome
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

        // Inserisce solo il nome (l'id è int autoincrementale)
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
        
        // Calcola la durata del corso
        $durataAnni = match ($corso->tipo_corso) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };

        // Recupera tutti gli insegnamenti disponibili da poter associare
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

        // Inserisce l'associazione nella tabella insegnamento_corso
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
        // 1. Recupera tutte le sessioni d'esame per popolare il menu/selettore nella vista
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        // 2. Se non è stata specificata una sessione nell'URL, seleziona la prima/più recente di default
        if (!$sessione_id && $sessioni->isNotEmpty()) {
            $sessione_id = $sessioni->first()->getKey(); 
        }

        // 3. Recupera tutti gli appelli d'esame per la sessione selezionata
        // Eager loading (with) per caricare i dettagli dell'insegnamento, corso e sessione
        $appelli = Appello::where('sessione_id', $sessione_id)->with(['insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso'])
            ->orderBy('data', 'asc')
            ->orderBy('ora_inizio', 'asc')
            ->get();

        // 4. Passa tutti i dati necessari alla vista Blade dell'admin
        return view('admin.calendario', compact('appelli', 'sessioni', 'sessione_id'));
    }
}
