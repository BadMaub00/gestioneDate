<?php

namespace App\Http\Controllers;

use App\Models\InsegnamentiCorso;
use Illuminate\Http\Request;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\DocenteInsegnamento;
use Illuminate\Support\Facades\Auth;

class appelloController extends Controller
{
    private function validationData(){
        return [
            'data' => 'required|date|after_or_equal:today',
            'ora_inizio' => 'required|date_format:H:i',
            'ora_fine' => 'required|date_format:H:i|after:ora_inizio',
            'luogo' => 'required|string|max:255',
            'ins_corso_id' => 'required|exists:insegnamento_corso,id', 
            'sessione_id' => 'required|exists:sessioni_esami,sessione_id', 
        ];
    }
    public function create(){
        // Logica per mostrare il form di creazione dell'appello
        /** @var \App\Models\DatiAccesso $user */
        $user = Auth::user();
        // 1. Recupera solo gli insegnamenti associati al docente loggato
        $insegnamentiCorsi = DocenteInsegnamento::where('docente_id', $user->getKey())
            ->where('ruolo', 'Primario')
            ->pluck('ins_corso_id')
            ->toArray();
        // 2. Recupera le sessioni d'esame attive
        $sessioni = Sessione::all();
        return view('appelli.create', compact('insegnamentiCorsi', 'sessioni'));
    }

    public function store(Request $request)
    {
        // 1. Validazione dei dati ricevuti dal form
        $validatedData = $request->validate($this->validationData());

        // 2. Controllo del numero di appelli esistenti per questo insegnamento e sessione
        $numAppelli = Appello::where('ins_corso_id', $validatedData['ins_corso_id'])
                            ->where('sessione_id', $validatedData['sessione_id'])
                            ->count();

        // Recupera la sessione per ottenere il numero massimo di appelli consentiti
        $sessione = Sessione::find($validatedData['sessione_id']);

        // Se la sessione esiste ed ha la variabile numero_appelli definita
        $maxAppelli = $sessione ? $sessione->numero_appelli : 2; // Usa 2 come fallback se non definita

        if ($numAppelli >= $maxAppelli) {
            // Se ci sono già il numero massimo di appelli, blocca il salvataggio
            return back()->withErrors(['ins_corso_id' => 'Ci sono già '.$maxAppelli.' appelli presenti in questa sessione!'])->withInput();
        }

        // Controllo sovrapposizione oraria
        $annoCorso = InsegnamentiCorso::where('ins_corso_id', $validatedData['ins_corso_id'])->value('anno_di_corso');
        $insSameAge = InsegnamentiCorso::where('anno_di_corso', $annoCorso)->pluck('id')->toArray();

        $orarioNonDisponibile = Appello::where('data', $validatedData['data'])
            ->whereIn('ins_corso_id', $insSameAge)
            ->where('ora_inizio', '<', $validatedData['ora_fine'])
            ->where('ora_fine', '>', $validatedData['ora_inizio'])
            ->exists();

        if ($orarioNonDisponibile) {
            return back()->withErrors(['data' => 'Esiste già un appello per un insegnamento dello stesso anno in questa data!'])->withInput();
        }

        // --- GENERAZIONE DELL'ID PERSONALIZZATO ---
        // Il suffisso sarà banalmente il numero di appelli già presenti + 1
        $suffisso = $numAppelli + 1;

        // Uniamo i valori in una stringa e li castiamo a numero intero ((int))
        $customId = (int) ($validatedData['ins_corso_id'] . $validatedData['sessione_id'] . $suffisso);

        // Iniettiamo l'ID nei dati validati (assicurati che la chiave 'id' corrisponda
        // al nome della colonna della chiave primaria nel database per il modello Appello)
        $validatedData['id'] = $customId;

        // Creazione dell'appello con l'ID calcolato
        Appello::create($validatedData);

        // 3. Reindirizzamento con messaggio di successo
        // ...
    }

    public function edit(int $id){

        $appello = Appello::findOrFail($id);

        $this->verificaDocenteAppello($appello, 'Non sei autorizzato a modificare questo appello.');

        // 3. Recupera le sessioni attive da mostrare nel menu a tendina
        $sessioni = Sessione::all(); 

        // 4. Passa tutti i dati necessari alla vista Blade
        return view('appelli.edit', compact('appello', 'sessioni'));
    }

    public function update(Request $request,int $id){
       
        $appello = Appello::findOrFail($id);
        
        $this->verificaDocenteAppello($appello, 'Non sei autorizzato a modificare questo appello.');
        
        // 3. Validazione dei dati ricevuti dal form
        $validatedData = $request->validate($this->validationData());

        // 4. Aggiornamento dell'appello
        $appello->update($validatedData);

        // 5. Reindirizzamento con messaggio di successo
        return redirect()->route('docente.dashboard')->with('success', 'Appello aggiornato con successo!');
    }

    public function destroy(int $id){
        $appello = Appello::findOrFail($id);

        $this->verificaDocenteAppello($appello, 'Non sei autorizzato a cancellare questo appello.');

        // 3. Cancellazione dell'appello
        $appello->delete();

        // 4. Reindirizzamento con messaggio di successo
        return redirect()->route('docente.dashboard')
            ->with('success', 'Appello cancellato con successo!');
    }

    public function verificaDocenteAppello(Appello $appello, string $msg){
        /** @var \App\Models\DatiAccesso $user */
        $user = Auth::user();


        // 2. CONTROLLO SICUREZZA:
        // Verifica che l'appello appartenga a un insegnamento associato al docente loggato
        // (passando per la relazione del tuo Model Utente/Docente)
        $insegnamentiDocenteIds = DocenteInsegnamento::where('docente_id', $user->getKey())
            ->where('ruolo', 'Primario')
            ->pluck('ins_corso_id')
            ->toArray();
            
        if (!in_array($appello->ins_corso_id, $insegnamentiDocenteIds)) {
            // Blocca l'accesso se l'appello appartiene a un altro docente
            abort(403, $msg);
        }
    }
}
