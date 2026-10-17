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
    public function dashboard() {
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        return view('admin.dashboard', compact('sessioni'));
    }

    public function indexCorsi() {
        $corsi = Corso::orderBy('nome', 'asc')->get();
        return view('admin.corsi.index', compact('corsi'));
    }

    public function viewPianoDiStudi($corso_id) {
        $corso = Corso::with(['insegnamenti' => fn($q) => $q->orderBy('anno_di_corso', 'asc')])->findOrFail($corso_id);
        $insegnamentiPerAnno = $corso->insegnamenti->groupBy('anno_di_corso');
        $durataAnni = $this->getDurataAnni($corso->tipo_corso);

        return view('admin.corsi.piano_di_studi', compact('corso', 'insegnamentiPerAnno', 'durataAnni'));
    }

    // --- PERSONALE ---

    public function indexPersonale() {
        $personale = Utente::orderBy('cognome', 'asc')->orderBy('nome', 'asc')->get();
        return view('admin.personale.index', compact('personale'));
    }

    public function createPersonale() {
        return view('admin.personale.create');
    }

    public function storePersonale(Request $request) {
        $data = $request->validate([
            'username'     => 'required|string|max:100|unique:utente,username',
            'password'     => 'required|string|min:6',
            'nome'         => 'required|string|max:100',
            'cognome'      => 'required|string|max:100',
            'data_nascita' => 'required|date',
            'ruolo'        => 'required|in:Docente,Amministratore',
        ]);

        $data['password'] = Hash::make($data['password']);
        Utente::create($data);

        return redirect()->route('admin.personale.index')->with('success', 'Membro del personale creato con successo!');
    }

    public function destroyPersonale($id) {
        Utente::findOrFail($id)->delete();
        return redirect()->route('admin.personale.index')->with('success', 'Membro del personale eliminato con successo!');
    }

    // --- ASSOCIAZIONI DOCENTE ---

    public function createAssociazioneDocente() {
        $docenti = Utente::where('ruolo', 'Docente')->orderBy('cognome', 'asc')->orderBy('nome', 'asc')->get();
        $insegnamentiCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->orderBy('corso_id', 'asc')->get();

        return view('admin.personale.associa', compact('docenti', 'insegnamentiCorso'));
    }

    public function indexAssociazioni() {
        $associazioni = DocenteInsegnamento::with(['docente', 'insegnamentiCorso.insegnamento', 'insegnamentiCorso.corso'])->get();
        return view('admin.personale.associazioni_index', compact('associazioni'));
    }

    public function storeAssociazioneDocente(Request $request) {
        $validated = $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'utente_id'    => 'required|exists:utente,utente_id',
            'ruolo'        => 'required|in:Primario,Secondario',
        ]);

        // 1. Controllo che l'utente sia effettivamente un Docente (impedisce l'assegnazione ad Amministratori)
        $utente = Utente::findOrFail($validated['utente_id']);
        if ($utente->ruolo !== 'Docente') {
            return back()->withErrors(['utente_id' => 'Solo gli utenti con ruolo "Docente" possono essere associati ad un insegnamento!'])->withInput();
        }

        // 2. Controllo associazione duplicata (impedisce che il docente sia sia Primario che Secondario per lo stesso insegnamento)
        $giaAssociato = DocenteInsegnamento::where('ins_corso_id', $validated['ins_corso_id'])
            ->where('utente_id', $validated['utente_id'])
            ->exists();

        if ($giaAssociato) {
            return back()->withErrors(['utente_id' => 'Questo docente è già associato a questo insegnamento!'])->withInput();
        }

        // 3. Controllo presenza di un solo Docente Primario per insegnamento
        if ($validated['ruolo'] === 'Primario') {
            $haGiaPrimario = DocenteInsegnamento::where('ins_corso_id', $validated['ins_corso_id'])
                ->where('ruolo', 'Primario')
                ->exists();

            if ($haGiaPrimario) {
                return back()->withErrors(['ruolo' => 'Questo insegnamento ha già un docente con ruolo Primario!'])->withInput();
            }
        }

        DocenteInsegnamento::create($validated);
        return redirect()->route('admin.personale.associazioni.index')->with('success', 'Docente associato con successo!');
    }

    public function destroyAssociazioneDocente($id) {
        DocenteInsegnamento::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Associazione rimossa con successo.');
    }

    // --- APPELLI ---

    public function editAppello($id) {
        $appello = Appello::with(['insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso'])->findOrFail($id);
        $sessioni = Sessione::all();
        return view('admin.appelli.edit', compact('appello', 'sessioni'));
    }

    public function updateAppello(Request $request, $id) {
        $appello = Appello::findOrFail($id);

        $validatedData = $request->validate([
            'data'       => 'required|date',
            'ora_inizio' => 'required|date_format:H:i|after_or_equal:08:00|before_or_equal:18:00',
            'ora_fine'   => 'required|date_format:H:i|after:ora_inizio|before_or_equal:19:00',
        ]);

        $sessione = Sessione::findOrFail($appello->sessione_id);

        if ($validatedData['data'] < $sessione->data_inizio || $validatedData['data'] > $sessione->data_fine) {
            return back()->withErrors([
                'data' => 'La data dev\'essere compresa tra l\'inizio (' . Carbon::parse($sessione->data_inizio)->format('d/m/Y') . ') e la fine (' . Carbon::parse($sessione->data_fine)->format('d/m/Y') . ') della sessione.'
            ])->withInput();
        }

        if ($erroreSovrapposizione = $this->verificaSovrapposizioneAppello($appello, $validatedData, $id)) {
            return back()->withErrors($erroreSovrapposizione)->withInput();
        }

        $appello->update($validatedData);
        return redirect()->route('admin.calendario', $appello->sessione_id)->with('success', 'Appello modificato con successo.');
    }

    public function destroyAppello($id) {
        $appello = Appello::findOrFail($id);
        $sessione_id = $appello->sessione_id;
        $appello->delete();

        return redirect()->route('admin.calendario', $sessione_id)->with('success', 'Appello eliminato con successo.');
    }

    // --- INSEGNAMENTI E CORSI ---

    public function indexInsegnamenti() {
        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();
        return view('admin.insegnamenti.index', compact('insegnamenti'));
    }

    public function createInsegnamento() {
        return view('admin.insegnamenti.create');
    }

    public function storeInsegnamento(Request $request) {
        Insegnamento::create($request->validate(['nome' => 'required|string|max:150']));
        return redirect()->route('admin.insegnamenti.index')->with('success', 'Insegnamento creato con successo!');
    }

    public function destroyInsegnamento($insegnamento_id) {
        Insegnamento::findOrFail($insegnamento_id)->delete();
        return redirect()->back()->with('success', 'Insegnamento eliminato con successo.');
    }

    public function createCorso() {
        return view('admin.corsi.create');
    }

    public function storeCorso(Request $request) {
        Corso::create($request->validate([
            'nome'       => 'required|string|max:150',
            'tipo_corso' => 'required|in:Triennale,Magistrale,Magistrale a ciclo unico',
        ]));
        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea creato con successo!');
    }

    public function createInsegnamentoCorso($corso_id) {
        $corso = Corso::findOrFail($corso_id);
        $durataAnni = $this->getDurataAnni($corso->tipo_corso);
        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();

        return view('admin.corsi.createInsCorso', compact('corso', 'durataAnni', 'insegnamenti'));
    }

    public function storeInsegnamentoCorso(Request $request, $corso_id) {
        $data = $request->validate([
            'insegnamento_id' => 'required|exists:insegnamento,insegnamento_id',
            'anno_di_corso'   => 'required|integer|min:1|max:5',
            'cfu'             => 'required|integer|min:1|max:30',
            'durata'          => 'required|integer|min:1',
        ]);

        // Controllo se l'insegnamento è già presente nel piano di studi di questo corso
        $giaPresente = InsegnamentiCorso::where('corso_id', $corso_id)
            ->where('insegnamento_id', $data['insegnamento_id'])
            ->exists();

        if ($giaPresente) {
            return back()
                ->withErrors(['insegnamento_id' => 'Questo insegnamento fa già parte del piano di studi di questo corso!'])
                ->withInput();
        }

        $data['corso_id'] = $corso_id;
        InsegnamentiCorso::create($data);

        return redirect()->route('admin.corsi.pianoDiStudi', $corso_id)
            ->with('success', 'Insegnamento aggiunto al piano di studi con successo!');
    }

    public function viewcalendario_generale($sessione_id = null) {
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        $sessione_id = $sessione_id ?? $sessioni->first()?->getKey();

        $appelli = Appello::where('sessione_id', $sessione_id)
            ->with(['insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso'])
            ->orderBy('data', 'asc')
            ->orderBy('ora_inizio', 'asc')
            ->get();

        return view('admin.calendario', compact('appelli', 'sessioni', 'sessione_id'));
    }

    // --- METODI HELPER PRIVATI ---

    private function getDurataAnni(string $tipoCorso): int {
        return match ($tipoCorso) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };
    }

    private function verificaSovrapposizioneAppello(Appello $appello, array $data, int $appelloId): ?array {
        $stessoInsegnamento = Appello::where('ins_corso_id', $appello->ins_corso_id)
            ->where('appello_id', '!=', $appelloId)
            ->where('data', $data['data'])
            ->where('ora_inizio', '<', $data['ora_fine'])
            ->where('ora_fine', '>', $data['ora_inizio'])
            ->exists();

        if ($stessoInsegnamento) {
            return ['ora_inizio' => 'Esiste già un appello per questo stesso insegnamento nello stesso intervallo orario!'];
        }

        $annoCorso = InsegnamentiCorso::where('ins_corso_id', $appello->ins_corso_id)->value('anno_di_corso');
        $insSameAge = InsegnamentiCorso::where('anno_di_corso', $annoCorso)->pluck('ins_corso_id')->toArray();

        $stessoAnno = Appello::where('data', $data['data'])
            ->where('appello_id', '!=', $appelloId)
            ->whereIn('ins_corso_id', $insSameAge)
            ->where('ora_inizio', '<', $data['ora_fine'])
            ->where('ora_fine', '>', $data['ora_inizio'])
            ->exists();

        if ($stessoAnno) {
            return ['data' => 'Esiste già un appello per un insegnamento dello stesso anno di corso in questa data e orario!'];
        }

        return null;
    }
}