<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\Utente;
use App\Models\Sessione;
use App\Models\Corso;

class docenteController extends Controller
{
    public function dashboard()
    {
        /** @var Utente $user */
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

        // Caricamento Eager della relazione aula ed edificio per ogni appello
        $insegnamentiCorsi->load(['appelli' => function($query) use ($sessione_id) {
            $query->where('sessione_id', $sessione_id)
                ->with('aula.edificio')
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
        $corsi = $insegnamentiCorsi->pluck('corso')->filter()->unique('corso_id')->values();
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

    public function viewcalendarioGenerale(Request $request)
    {
        /** @var Utente|null $user */
        $user = Auth::user();
        $docenteId = $user ? $user->getKey() : null;

        $sessioneId = $request->input('sessione_id') ?? $request->route('sessione_id');
        $corsoId = $request->input('corso_id') ?? $request->route('corso_id');
        $annoCorso = $request->input('annoCorso') ?? $request->input('anno_corso') ?? $request->route('annoCorso');

        $sessione = Sessione::find($sessioneId);
        $corso = Corso::find($corsoId);

        if (!$sessione || !$corso) {
            return redirect()->route('docente.selezionaFiltriGenerale')->withErrors(['msg' => 'Sessione o corso selezionato non valido.']);
        }

        // Filtra gli appelli relativi allo stesso corso, anno e sessione (in ordine cronologico)
        $appelliRaw = Appello::where('sessione_id', $sessione->sessione_id)
            ->whereHas('insegnamentoCorso', function ($q) use ($corsoId, $annoCorso) {
                $q->where('corso_id', $corsoId)
                  ->where('anno_di_corso', $annoCorso);
            })
            ->with(['insegnamentoCorso.insegnamento', 'aula.edificio'])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        // Due sole query invece di una per ogni appello:
        // insegnamenti del docente loggato...
        $insCorsoIds = $appelliRaw->pluck('ins_corso_id')->unique()->values();

        $mieiInsegnamenti = DocenteInsegnamento::where('utente_id', $docenteId)
            ->whereIn('ins_corso_id', $insCorsoIds)
            ->pluck('ins_corso_id')
            ->flip();

        // ...e docente di riferimento di ciascuno (il Primario, se presente)
        $docentiRiferimento = DocenteInsegnamento::whereIn('docente_insegnamento.ins_corso_id', $mieiInsegnamenti->keys())
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->orderByRaw("docente_insegnamento.ruolo = 'Primario' DESC")
            ->orderBy('docente_insegnamento.doc_ins_id')
            ->select('docente_insegnamento.ins_corso_id', 'utente.nome', 'utente.cognome')
            ->get()
            ->unique('ins_corso_id')
            ->keyBy('ins_corso_id');

        // Visibilità differenziata: dettagli solo per i propri appelli, "Occupato" per gli altri
        $appelliAnnoCorso = $appelliRaw->map(function ($appello) use ($mieiInsegnamenti, $docentiRiferimento) {
            $isMio = $mieiInsegnamenti->has($appello->ins_corso_id);
            $docenteObj = $isMio ? $docentiRiferimento->get($appello->ins_corso_id) : null;

            return (object) [
                'appello_id'        => $appello->appello_id,
                'data'              => $appello->data,
                'ora_inizio'        => $appello->ora_inizio,
                'ora_fine'          => $appello->ora_fine,
                'is_mio_appello'    => $isMio,
                'nome_insegnamento' => $isMio ? ($appello->insegnamentoCorso->insegnamento->nome ?? 'Insegnamento') : 'Occupato',
                'docente_nome'      => $isMio ? ($docenteObj->nome ?? '') : '',
                'docente_cognome'   => $isMio ? ($docenteObj->cognome ?? '') : '',
                'nome_aula'         => $isMio ? ($appello->aula->nome ?? 'N/D') : 'Occupata',
                'nome_edificio'     => $isMio ? ($appello->aula->edificio->nome ?? 'N/D') : '',
            ];
        });

        return view('docente.calendario_generale', compact(
            'corso',
            'sessione',
            'annoCorso',
            'appelliAnnoCorso'
        ));
    }

    public function dettaglioInsegnamento(int $ins_corso_id)
    {
        /** @var Utente $user */
        $user = Auth::user();

        $insegnamentoCorso = $this->insAssociati($user)->firstWhere('ins_corso_id', $ins_corso_id);

        if (!$insegnamentoCorso) {
            abort(404, 'Insegnamento non trovato o non associato a questo docente.');
        }

        $docenteInsegnamento = DocenteInsegnamento::where('ins_corso_id', $ins_corso_id)
            ->where('utente_id', $user->getKey())
            ->first();

        $docentePrimario = DocenteInsegnamento::where('docente_insegnamento.ins_corso_id', $ins_corso_id)
            ->where('docente_insegnamento.ruolo', 'Primario')
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->select('utente.utente_id', 'utente.nome', 'utente.cognome', 'docente_insegnamento.ruolo')
            ->first();

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

        $insegnamentoCorso = $this->insAssociati($user)->firstWhere('ins_corso_id', $ins_corso_id);

        if (!$insegnamentoCorso) {
            abort(404, 'Insegnamento non trovato o non associato a questo docente.');
        }

        $docenteInsegnamento = DocenteInsegnamento::where('ins_corso_id', $ins_corso_id)
            ->where('utente_id', $user->getKey())
            ->first();

        $sessioni = Sessione::orderBy('data_inizio', 'asc')->get();

        // Inclusione delle relazioni aula ed edificio per gli appelli della sessione
        foreach ($sessioni as $sessione) {
            $sessione->appelli_insegnamento = Appello::where('ins_corso_id', $ins_corso_id)
                ->where('sessione_id', $sessione->sessione_id)
                ->with('aula.edificio')
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
}
