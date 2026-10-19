<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use App\Models\Sessione;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\Aula;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Auth;

class appelloController extends Controller
{
    /**
     * Verifica se il docente loggato è proprietario dell'insegnamento legato all'appello.
     * Gli amministratori saltano questo controllo.
     */
    private function verificaDocenteAppello(Appello $appello)
    {
        /** @var \App\Models\Utente|null $user */
        $user = Auth::user();

        // Se l'utente è un amministratore, ha permessi di gestione completi
        if ($user && $user->ruolo === 'admin') {
            return;
        }

        // Verifica se l'insegnamento è associato al docente autenticato
        $isMioInsegnamento = DocenteInsegnamento::where('ins_corso_id', $appello->ins_corso_id)
            ->where('utente_id', $user->getKey())
            ->exists();

        if (!$isMioInsegnamento) {
            abort(403, 'Non sei autorizzato a modificare o eliminare questo appello.');
        }
    }

    /**
     * Valida tutti i vincoli dell'appello:
     * 1. Date all'interno della sessione
     * 2. Orari nella fascia 08:00 - 19:00
     * 3. Limite massimo di appelli per la sessione
     * 4. Assenza di sovrapposizioni per lo stesso Corso e Anno di Corso
     */
    private function validaVincoliAppello(Request $request, $appelloIdEscluso = null)
    {
        $sessione = Sessione::find($request->sessione_id);
        if (!$sessione) {
            return "Sessione d'esame non valida.";
        }

        $insCorso = InsegnamentiCorso::find($request->ins_corso_id);
        if (!$insCorso) {
            return "Insegnamento selezionato non valido.";
        }

        // 1. Controllo che la data sia compresa tra inizio e fine sessione
        if ($request->data < $sessione->data_inizio || $request->data > $sessione->data_fine) {
            return "La data dell'appello deve essere compresa tra {$sessione->data_inizio} e {$sessione->data_fine}.";
        }

        // 2. Controllo fascia oraria 08:00 - 19:00
        if ($request->ora_inizio < '08:00' || $request->ora_fine > '19:00' || $request->ora_inizio >= $request->ora_fine) {
            return "L'orario dell'appello deve essere compreso nella fascia 08:00 - 19:00.";
        }

        // 3. Controllo del limite 'numero_appelli' per la sessione
        $countAppelli = Appello::where('sessione_id', $sessione->sessione_id)
            ->where('ins_corso_id', $request->ins_corso_id)
            ->when($appelloIdEscluso, fn($q) => $q->where('appello_id', '!=', $appelloIdEscluso))
            ->count();

        if ($countAppelli >= $sessione->numero_appelli) {
            return "È stato raggiunto il numero massimo di appelli ({$sessione->numero_appelli}) per questa sessione.";
        }

        // 4. Controllo sovrapposizione oraria filtrando per STESSO corso_id e STESSO anno_di_corso
        $sovrapposto = Appello::join('insegnamenti_corsi', 'appello.ins_corso_id', '=', 'insegnamenti_corsi.ins_corso_id')
            ->where('appello.sessione_id', $request->sessione_id)
            ->where('insegnamenti_corsi.corso_id', $insCorso->corso_id)
            ->where('insegnamenti_corsi.anno_di_corso', $insCorso->anno_di_corso)
            ->where('appello.data', $request->data)
            ->where(function ($q) use ($request) {
                $q->where('appello.ora_inizio', '<', $request->ora_fine)
                  ->where('appello.ora_fine', '>', $request->ora_inizio);
            })
            ->when($appelloIdEscluso, fn($q) => $q->where('appello.appello_id', '!=', $appelloIdEscluso))
            ->exists();

        if ($sovrapposto) {
            return "Impossibile salvare l'appello: esiste già un'altra prova prevista per lo stesso Corso e Anno di Corso in questa data e fascia oraria.";
        }

        return null;
    }

    /**
     * Elenco appelli
     */
    public function index()
    {
        /** @var \App\Models\Utente|null $user */
        $user = Auth::user();

        if ($user && $user->ruolo === 'admin') {
            $appelli = Appello::with(['sessione', 'insegnamentoCorso.insegnamento', 'aula'])->get();
        } else {
            $insCorsoIds = DocenteInsegnamento::where('utente_id', $user->getKey())->pluck('ins_corso_id');
            $appelli = Appello::whereIn('ins_corso_id', $insCorsoIds)
                ->with(['sessione', 'insegnamentoCorso.insegnamento', 'aula'])->get();
        }

        return view('admin.appelli.index', compact('appelli'));
    }

    /**
     * Form creazione appello
     */
    public function create()
    {
        $sessioni = Sessione::all();
        $aule = Aula::all();

        /** @var \App\Models\Utente|null $user */
        $user = Auth::user();

        if ($user && $user->ruolo === 'admin') {
            $insegnamentiCorsi = InsegnamentiCorso::with(['insegnamento', 'corso'])->get();
        } else {
            $insCorsoIds = DocenteInsegnamento::where('utente_id', $user->getKey())->pluck('ins_corso_id');
            $insegnamentiCorsi = InsegnamentiCorso::whereIn('ins_corso_id', $insCorsoIds)
                ->with(['insegnamento', 'corso'])->get();
        }

        return view('docente.appelli.create', compact('sessioni', 'aule', 'insegnamentiCorsi'));
    }

    /**
     * Salvataggio nuovo appello
     */
    public function store(Request $request)
    {
        $request->validate([
            'ins_corso_id' => 'required|exists:insegnamenti_corsi,ins_corso_id',
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
            'data'         => 'required|date',
            'ora_inizio'   => 'required',
            'ora_fine'     => 'required|after:ora_inizio',
            'aula_id'      => 'required|exists:aula,aula_id',
        ]);

        /** @var \App\Models\Utente|null $user */
        $user = Auth::user();

        // Se è un docente, verifica che l'insegnamento appartenga a lui prima di crearlo
        if ($user && $user->ruolo !== 'admin') {
            $isMio = DocenteInsegnamento::where('ins_corso_id', $request->ins_corso_id)
                ->where('utente_id', $user->getKey())
                ->exists();

            if (!$isMio) {
                return redirect()->back()->withInput()->withErrors(['ins_corso_id' => 'Non puoi inserire appelli per insegnamenti che non ti appartengono.']);
            }
        }

        // Controllo unificato di tutti i vincoli
        $errore = $this->validaVincoliAppello($request);
        if ($errore) {
            return redirect()->back()->withInput()->withErrors(['vincolo' => $errore]);
        }

        Appello::create($request->all());

        return redirect()->route('docente.dashboard')->with('success', 'Appello inserito con successo.');
    }

    /**
     * Form modifica appello
     */
    public function edit($id)
    {
        $appello = Appello::findOrFail($id);

        // Controllo di proprietà
        $this->verificaDocenteAppello($appello);

        $sessioni = Sessione::all();
        $aule = Aula::all();
        $insegnamentiCorsi = InsegnamentiCorso::with(['insegnamento', 'corso'])->get();

        return view('docente.appelli.edit', compact('appello', 'sessioni', 'aule', 'insegnamentiCorsi'));
    }

    /**
     * Aggiornamento appello
     */
    public function update(Request $request, $id)
    {
        $appello = Appello::findOrFail($id);

        // Controllo di proprietà
        $this->verificaDocenteAppello($appello);

        $request->validate([
            'ins_corso_id' => 'required|exists:insegnamenti_corsi,ins_corso_id',
            'sessione_id'  => 'required|exists:sessione-esami,sessione_id',
            'data'         => 'required|date',
            'ora_inizio'   => 'required',
            'ora_fine'     => 'required|after:ora_inizio',
            'aula_id'      => 'required|exists:aula,aula_id',
        ]);

        // Controllo vincoli escludendo l'appello in fase di modifica
        $errore = $this->validaVincoliAppello($request, $appello->appello_id);
        if ($errore) {
            return redirect()->back()->withInput()->withErrors(['vincolo' => $errore]);
        }

        $appello->update($request->all());

        return redirect()->route('docente.dashboard')->with('success', 'Appello aggiornato con successo.');
    }

    /**
     * Eliminazione appello
     */
    public function destroy($id)
    {
        $appello = Appello::findOrFail($id);

        // Controllo di proprietà
        $this->verificaDocenteAppello($appello);

        $appello->delete();

        return redirect()->back()->with('success', 'Appello eliminato con successo.');
    }
}