<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\Corso;
use App\Models\Utente;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;

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
