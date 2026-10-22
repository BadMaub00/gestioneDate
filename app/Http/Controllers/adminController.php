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
use App\Models\Edificio;
use App\Models\Aula;
use Illuminate\Validation\Rule;
use App\Services\AppelloRules;
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

    public function editPersonale($id) {
        $utente = Utente::findOrFail($id);
        return view('admin.personale.edit', compact('utente'));
    }

    public function updatePersonale(Request $request, $id) {
        $utente = Utente::findOrFail($id);

        $validated = $request->validate([
            'username'     => 'required|string|max:100|unique:utente,username,' . $id . ',utente_id',
            'nome'         => 'required|string|max:100',
            'cognome'      => 'required|string|max:100',
            'data_nascita' => 'required|date',
            'ruolo'        => 'required|in:Docente,Amministratore',
            'password'     => 'nullable|string|min:6',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $utente->update($validated);

        return redirect()->route('admin.personale.index')->with('success', 'Membro del personale aggiornato con successo!');
    }

    // --- ASSOCIAZIONI DOCENTE ---

    public function createAssociazioneDocente() {
        $docenti = Utente::where('ruolo', 'Docente')->orderBy('cognome', 'asc')->orderBy('nome', 'asc')->get();
        $insegnamentiCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->orderBy('corso_id', 'asc')->get();

        return view('admin.personale.associa', compact('docenti', 'insegnamentiCorso'));
    }

    public function indexAssociazioni(Request $request) {
        $query = DocenteInsegnamento::with([
            'docente', 
            'insegnamentiCorso.insegnamento', 
            'insegnamentiCorso.corso'
        ]);

        // 1. Filtro facoltativo per Corso di Laurea
        if ($request->filled('corso_id')) {
            $query->whereHas('insegnamentiCorso', function ($q) use ($request) {
                $q->where('corso_id', $request->corso_id);
            });
        }

        // 2. Filtro facoltativo per Insegnamento specifico
        if ($request->filled('ins_corso_id')) {
            $query->where('ins_corso_id', $request->ins_corso_id);
        }

        // 3. Filtro facoltativo per Docente
        if ($request->filled('docente_id')) {
            $query->where('utente_id', $request->docente_id);
        }

        $associazioni = $query->get();

        // Raggruppamento e ordinamento per nome del corso
        $associazioniGrouped = $associazioni->sortBy([
            fn ($a, $b) => strcmp($a->insegnamentiCorso->corso->nome ?? '', $b->insegnamentiCorso->corso->nome ?? ''),
            fn ($a, $b) => strcmp($a->docente->cognome ?? '', $b->docente->cognome ?? ''),
            fn ($a, $b) => strcmp($a->docente->nome ?? '', $b->docente->nome ?? ''),
        ])
        ->groupBy(fn ($assoc) => $assoc->insegnamentiCorso->corso->nome ?? 'N/D')
        ->sortKeys();

        // Dati per ripopolare i menu a tendina nei filtri
        $corsi = Corso::orderBy('nome', 'asc')->get();
        $insegnamentiCorso = InsegnamentiCorso::with('insegnamento')->get();
        $docenti = Utente::where('ruolo', 'Docente')->orderBy('cognome', 'asc')->get();

        return view('admin.personale.associazioni_index', compact(
            'associazioniGrouped', 
            'corsi', 
            'insegnamentiCorso', 
            'docenti'
        ));
    }

    public function storeAssociazioneDocente(Request $request) {
        $validated = $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'utente_id'    => 'required|exists:utente,utente_id',
            'ruolo'        => 'required|in:Primario,Secondario',
        ]);

        // 1. Controllo ruolo docente
        $utente = Utente::findOrFail($validated['utente_id']);
        if ($utente->ruolo !== 'Docente') {
            return back()->withErrors(['utente_id' => 'Solo gli utenti con ruolo "Docente" possono essere associati ad un insegnamento!'])->withInput();
        }

        // 2. Controllo associazione duplicata
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
        return redirect()->route('admin.personale.associazioni_index.index')->with('success', 'Docente associato con successo!');
    }

    public function destroyAssociazioneDocente($id) {
        DocenteInsegnamento::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Associazione rimossa con successo.');
    }

    // --- APPELLI ---

    public function editAppello($id) {
        $appello = Appello::with([
            'sessione',
            'insegnamentoCorso.insegnamento', 
            'insegnamentoCorso.corso',
            'aula.edificio'
        ])->findOrFail($id);

        // Blocco se la sessione è già passata
        if (Carbon::parse($appello->sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare un appello appartenente a una sessione passata.');
        }

        $sessioni = Sessione::all();
        $edifici = Edificio::with('aule')->orderBy('nome', 'asc')->get();
        $aule = Aula::orderBy('nome', 'asc')->get();

        return view('admin.appelli.edit', compact('appello', 'sessioni', 'edifici', 'aule'));
    }

    public function updateAppello(Request $request, $id) {
        $appello = Appello::with(['insegnamentoCorso', 'sessione'])->findOrFail($id);
        $sessione = $appello->sessione;

        // Blocco se la sessione è passata
        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare un appello appartenente a una sessione passata.');
        }

        $validatedData = $request->validate(
            AppelloRules::regole($sessione) + [
                'edificio_id' => 'required|exists:edificio,edificio_id',
                // l'aula deve appartenere all'edificio scelto
                'aula_id'     => ['required', Rule::exists('aula', 'aula_id')->where('edificio_id', $request->input('edificio_id'))],
            ],
            AppelloRules::messaggi($sessione) + [
                'aula_id.exists' => "L'aula selezionata non appartiene all'edificio scelto.",
            ]
        );

        if ($errore = AppelloRules::conflitto((int) $appello->ins_corso_id, $validatedData, (int) $id)) {
            return back()->withErrors($errore)->withInput();
        }

        $appello->update([
            'data'       => $validatedData['data'],
            'ora_inizio' => $validatedData['ora_inizio'],
            'ora_fine'   => $validatedData['ora_fine'],
            'aula_id'    => $validatedData['aula_id'],
        ]);

        return redirect()->route('admin.calendario', [
            'sessione_id' => $appello->sessione_id,
            'corso_id'    => $appello->insegnamentoCorso->corso_id ?? null,
        ])->with('success', 'Appello modificato con successo.');
    }

    public function destroyAppello($id) {
        $appello = Appello::with(['insegnamentoCorso', 'sessione'])->findOrFail($id);

        if (Carbon::parse($appello->sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile eliminare un appello appartenente a una sessione passata.');
        }

        $sessione_id = $appello->sessione_id;
        $corso_id = $appello->insegnamentoCorso->corso_id ?? null;
        $appello->delete();

        return redirect()->route('admin.calendario', [
            'sessione_id' => $sessione_id,
            'corso_id'    => $corso_id,
        ])->with('success', 'Appello eliminato con successo.');
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

    public function editInsegnamento($insegnamento_id) {
        $insegnamento = Insegnamento::findOrFail($insegnamento_id);
        return view('admin.insegnamenti.edit', compact('insegnamento'));
    }

    public function updateInsegnamento(Request $request, $insegnamento_id) {
        $insegnamento = Insegnamento::findOrFail($insegnamento_id);

        $validated = $request->validate([
            'nome' => 'required|string|max:150|unique:insegnamento,nome,' . $insegnamento_id . ',insegnamento_id',
        ]);

        $insegnamento->update($validated);

        return redirect()->route('admin.insegnamenti.index')->with('success', 'Insegnamento aggiornato con successo!');
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

    public function createInsegnamentoCorso($corso_id)
    {
        $corso = Corso::findOrFail($corso_id);
        $insegnamenti = Insegnamento::all();

        return view('admin.corsi.createInsCorso', compact('corso', 'insegnamenti'));
    }

    public function storeInsegnamentoCorso(Request $request, $corso_id) {
        $corso = Corso::findOrFail($corso_id);
        $durataAnni = $this->getDurataAnni($corso->tipo_corso);

        $data = $request->validate([
            'insegnamento_id' => 'required|exists:insegnamento,insegnamento_id',
            'anno_di_corso'   => "required|integer|min:1|max:{$durataAnni}",
            'cfu'             => 'required|integer|min:1|max:30',
            'durata'          => 'required|integer|min:1',
        ]);

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

        return redirect()->route('admin.corsi.piano_di_studi', $corso_id)
            ->with('success', 'Insegnamento aggiunto al piano di studi con successo!');
    }

    public function editCorso($corso_id) {
        $corso = Corso::findOrFail($corso_id);
        return view('admin.corsi.edit', compact('corso'));
    }

    public function updateCorso(Request $request, $corso_id) {
        $corso = Corso::findOrFail($corso_id);

        $validated = $request->validate([
            'nome'       => 'required|string|max:150',
            'tipo_corso' => 'required|in:Triennale,Magistrale,Magistrale a ciclo unico',
        ]);

        $corso->update($validated);

        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea aggiornato con successo!');
    }

    // --- CALENDARIO ---

    public function viewcalendario_generale(Request $request) {
        $sessione_id = $request->input('sessione_id');
        $corso_id = $request->input('corso_id');
        $anno_di_corso = $request->input('anno_di_corso');

        // Recupera sessioni in ordine di data di inizio
        $sessioni = Sessione::orderBy('data_inizio', 'asc')->get();
        
        // Recupera corsi in ordine alfabetico e calcola la durata
        $corsi = Corso::orderBy('nome', 'asc')->get();
        foreach ($corsi as $corso) {
            $corso->durata_anni = $this->getDurataAnni($corso->tipo_corso);
        }

        $query = Appello::with([
            'sessione',
            'insegnamentoCorso.insegnamento',
            'insegnamentoCorso.corso',
            'aula.edificio'
        ]);

        // Applicazione filtri facoltativi
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

        // Ordinamento multi-livello:
        // 1. Sessione per data_inizio
        // 2. Corso di laurea per nome (alfabetico)
        // 3. Anno di corso (numerico)
        // 4. Insegnamento per nome (alfabetico)
        // 5. Data e ora appello (cronologico)
        $appelli = $query->get()->sortBy([
            fn ($a, $b) => strcmp($a->sessione->data_inizio ?? '', $b->sessione->data_inizio ?? ''),
            fn ($a, $b) => strcmp($a->insegnamentoCorso->corso->nome ?? '', $b->insegnamentoCorso->corso->nome ?? ''),
            fn ($a, $b) => ($a->insegnamentoCorso->anno_di_corso ?? 0) <=> ($b->insegnamentoCorso->anno_di_corso ?? 0),
            fn ($a, $b) => strcmp($a->insegnamentoCorso->insegnamento->nome ?? '', $b->insegnamentoCorso->insegnamento->nome ?? ''),
            fn ($a, $b) => strcmp($a->data . $a->ora_inizio, $b->data . $b->ora_inizio),
        ]);

        // Raggruppamento gerarchico annidato: Sessione ID -> Nome Corso -> Anno di Corso
        $appelliGrouped = $appelli->groupBy([
            fn ($item) => $item->sessione_id,
            fn ($item) => $item->insegnamentoCorso->corso->nome ?? 'Senza Corso',
            fn ($item) => $item->insegnamentoCorso->anno_di_corso ?? 'Non Specificato',
        ]);

        return view('admin.calendario', compact(
            'appelliGrouped', 
            'sessioni', 
            'corsi', 
            'sessione_id', 
            'corso_id', 
            'anno_di_corso'
        ));
    }

    // --- GESTIONE EDIFICI ---

    public function indexEdifici() {
        $edifici = Edificio::orderBy('nome', 'asc')->get();
        return view('admin.sedi.edifici', compact('edifici'));
    }

    public function viewAuleEdificio($edificio_id) {
        $edificio = Edificio::findOrFail($edificio_id);
        $aule = Aula::where('edificio_id', $edificio_id)
            ->orderBy('nome', 'asc')
            ->get();

        return view('admin.sedi.aule', compact('edificio', 'aule'));
    }


        public function createEdificio() {
            return view('admin.sedi.createEdificio');
        }

        public function storeEdificio(Request $request) {
            $validated = $request->validate([
                'nome' => 'required|string|max:150|unique:edificio,nome',
                'via'  => 'required|string|max:255',
            ]);

            Edificio::create($validated);

            return redirect()->route('admin.edifici.index')->with('success', 'Edificio creato con successo!');
        }

        public function editEdificio($id) {
            $edificio = Edificio::findOrFail($id);
            return view('admin.sedi.editEdificio', compact('edificio'));
        }

        public function updateEdificio(Request $request, $id) {
            $edificio = Edificio::findOrFail($id);

            $validated = $request->validate([
                'nome' => 'required|string|max:150|unique:edificio,nome,' . $id . ',edificio_id',
                'via'  => 'required|string|max:255',
            ]);

            $edificio->update($validated);

            return redirect()->route('admin.edifici.index')->with('success', 'Edificio aggiornato con successo!');
        }

        public function destroyEdificio($id) {
            $edificio = Edificio::findOrFail($id);
            $edificio->delete();

            return redirect()->route('admin.edifici.index')->with('success', 'Edificio eliminato con successo!');
        }

        // --- GESTIONE AULE ---

        public function createAula($edificio_id) {
            $edificio = Edificio::findOrFail($edificio_id);
            return view('admin.sedi.createAula', compact('edificio'));
        }

        public function storeAula(Request $request, $edificio_id) {
            $edificio = Edificio::findOrFail($edificio_id);

            $validated = $request->validate([
                'nome'     => 'required|string|max:100',
                'capienza' => 'required|integer|min:1',
            ]);

            $validated['edificio_id'] = $edificio->edificio_id;

            Aula::create($validated);

            return redirect()->route('admin.sedi.aule', $edificio_id)
                ->with('success', 'Aula creata con successo!');
        }

        public function editAula($id) {
            $aula = Aula::with('edificio')->findOrFail($id);
            return view('admin.sedi.editAula', compact('aula'));
        }

        public function updateAula(Request $request, $id) {
            $aula = Aula::findOrFail($id);

            $validated = $request->validate([
                'nome'     => 'required|string|max:100',
                'capienza' => 'required|integer|min:1',
            ]);

            $aula->update($validated);

            return redirect()->route('admin.sedi.aule', $aula->edificio_id)
                ->with('success', 'Aula aggiornata con successo!');
        }

        public function destroyAula($id) {
            $aula = Aula::findOrFail($id);
            $edificio_id = $aula->edificio_id;
            $aula->delete();

            return redirect()->route('admin.sedi.aule', $edificio_id)
                ->with('success', 'Aula eliminata con successo!');
        }

    // --- METODI HELPER PRIVATI ---

    private function getDurataAnni(string $tipoCorso): int {
        return match ($tipoCorso) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };
    }
}
