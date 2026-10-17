<?php

namespace App\Http\Controllers;

use App\Models\InsegnamentiCorso;
use Illuminate\Http\Request;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\DocenteInsegnamento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class appelloController extends Controller
{
    private function validationData(){
        return [
            'data'         => 'required|date|after_or_equal:today',
            'ora_inizio'   => 'required|date_format:H:i|after_or_equal:08:00|before_or_equal:18:00',
            'ora_fine'     => 'required|date_format:H:i|after:ora_inizio|before_or_equal:19:00',
            //luogo??
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id', 
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id', 
        ];
    }

    public function create(){
        /** @var \App\Models\Utente $user */
        $user = Auth::user();
        
        $insegnamentiCorsiIds = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->where('ruolo', 'Primario')
            ->pluck('ins_corso_id');

        $insegnamentiCorsi = InsegnamentiCorso::with(['insegnamento', 'corso'])
            ->whereIn('ins_corso_id', $insegnamentiCorsiIds)
            ->get();

        $sessioni = Sessione::all();
        
        return view('appelli.create', compact('insegnamentiCorsi', 'sessioni'));
    }

    public function store(Request $request)
    {
        // 1. Validazione base dei campi e orari
        $validatedData = $request->validate($this->validationData());

        $sessione = Sessione::findOrFail($validatedData['sessione_id']);

        // 2. Controllo preavviso 30 giorni prima dell'inizio della sessione
        $limiteCreazione = Carbon::parse($sessione->data_inizio)->subDays(30);
        if (Carbon::today()->gte($limiteCreazione)) {
            return back()->withErrors([
                'sessione_id' => 'Non è più possibile inserire appelli per questa sessione: devono essere creati almeno 30 giorni prima del suo inizio (' . $limiteCreazione->format('d/m/Y') . ').'
            ])->withInput();
        }

        // 3. Controllo data compresa nei limiti della sessione
        if ($validatedData['data'] < $sessione->data_inizio || $validatedData['data'] > $sessione->data_fine) {
            return back()->withErrors([
                'data' => 'La data dell\'appello deve essere compresa tra l\'inizio (' . Carbon::parse($sessione->data_inizio)->format('d/m/Y') . ') e la fine (' . Carbon::parse($sessione->data_fine)->format('d/m/Y') . ') della sessione.'
            ])->withInput();
        }

        // 4. Controllo numero massimo appelli dinamico basato su $sessione->numero_appelli
        $numAppelli = Appello::where('ins_corso_id', $validatedData['ins_corso_id'])
                            ->where('sessione_id', $validatedData['sessione_id'])
                            ->count();

        $maxAppelli = $sessione->numero_appelli;

        if ($numAppelli >= $maxAppelli) {
            return back()->withErrors(['ins_corso_id' => 'Ci sono già '.$maxAppelli.' appelli presenti in questa sessione!'])->withInput();
        }

        // 5. Controllo sovrapposizione per lo STESSO insegnamento nello stesso slot
        $stessoInsegnamentoSovrapposto = Appello::where('ins_corso_id', $validatedData['ins_corso_id'])
            ->where('data', $validatedData['data'])
            ->where('ora_inizio', '<', $validatedData['ora_fine'])
            ->where('ora_fine', '>', $validatedData['ora_inizio'])
            ->exists();

        if ($stessoInsegnamentoSovrapposto) {
            return back()->withErrors(['ora_inizio' => 'Esiste già un appello per questo stesso insegnamento nello stesso intervallo orario!'])->withInput();
        }

        // 6. Controllo sovrapposizione oraria tra insegnamenti dello stesso anno di corso
        $annoCorso = InsegnamentiCorso::where('ins_corso_id', $validatedData['ins_corso_id'])->value('anno_di_corso');
        $insSameAge = InsegnamentiCorso::where('anno_di_corso', $annoCorso)->pluck('ins_corso_id')->toArray();

        $orarioNonDisponibile = Appello::where('data', $validatedData['data'])
            ->whereIn('ins_corso_id', $insSameAge)
            ->where('ora_inizio', '<', $validatedData['ora_fine'])
            ->where('ora_fine', '>', $validatedData['ora_inizio'])
            ->exists();

        if ($orarioNonDisponibile) {
            return back()->withErrors(['data' => 'Esiste già un appello per un insegnamento dello stesso anno di corso in questa data e orario!'])->withInput();
        }

        // Salvataggio dell'appello
        Appello::create($validatedData);

        return redirect()->route('docente.dashboard')->with('success', 'Appello creato con successo!');
    }

    public function edit(int $id){
        $appello = Appello::findOrFail($id);

        $this->verificaDocenteAppello($appello, 'Non sei autorizzato a modificare questo appello.');

        $sessioni = Sessione::all(); 

        return view('appelli.edit', compact('appello', 'sessioni'));
    }

    public function update(Request $request, int $id){
        $appello = Appello::findOrFail($id);
        
        $this->verificaDocenteAppello($appello, 'Non sei autorizzato a modificare questo appello.');
        
        $validatedData = $request->validate($this->validationData());

        $sessione = Sessione::findOrFail($validatedData['sessione_id']);

        // Controllo data compresa nella sessione
        if ($validatedData['data'] < $sessione->data_inizio || $validatedData['data'] > $sessione->data_fine) {
            return back()->withErrors([
                'data' => 'La data dell\'appello deve essere compresa nella sessione selezionata.'
            ])->withInput();
        }

        // Controllo sovrapposizione per lo STESSO insegnamento nello stesso slot (escludendo l'appello corrente)
        $stessoInsegnamentoSovrapposto = Appello::where('ins_corso_id', $validatedData['ins_corso_id'])
            ->where('appello_id', '!=', $id)
            ->where('data', $validatedData['data'])
            ->where('ora_inizio', '<', $validatedData['ora_fine'])
            ->where('ora_fine', '>', $validatedData['ora_inizio'])
            ->exists();

        if ($stessoInsegnamentoSovrapposto) {
            return back()->withErrors(['ora_inizio' => 'Esiste già un appello per questo insegnamento nello stesso intervallo orario!'])->withInput();
        }

        $appello->update($validatedData);

        return redirect()->route('docente.dashboard')->with('success', 'Appello aggiornato con successo!');
    }

    public function destroy(int $id){
        $appello = Appello::findOrFail($id);

        $this->verificaDocenteAppello($appello, 'Non sei autorizzato a cancellare questo appello.');

        $appello->delete();

        return redirect()->route('docente.dashboard')
            ->with('success', 'Appello cancellato con successo!');
    }

    public function verificaDocenteAppello(Appello $appello, string $msg){
        /** @var \App\Models\Utente $user */
        $user = Auth::user();

        $insegnamentiDocenteIds = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->where('ruolo', 'Primario')
            ->pluck('ins_corso_id')
            ->toArray();
            
        if (!in_array($appello->ins_corso_id, $insegnamentiDocenteIds)) {
            abort(403, $msg);
        }
    }
}