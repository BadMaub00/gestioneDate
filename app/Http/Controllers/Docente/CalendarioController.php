<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Appello;
use App\Models\Corso;
use App\Models\DocenteInsegnamento;
use App\Models\InsegnamentiCorso;
use App\Models\Sessione;
use App\Services\InsegnamentiDocente;
use Illuminate\Http\Request;

/** Calendari (personale e generale) e relativi filtri, visti dal docente loggato. */
class CalendarioController extends Controller
{
    public function __construct(private InsegnamentiDocente $insegnamenti)
    {
    }

    // ---------------------------------------------------------------- calendario personale

    public function selezionaSessione(Request $request)
    {
        [$corsi, $sessioni] = $this->corsiESessioni($request);

        return view('docente.selezionaSessione', compact('corsi', 'sessioni'));
    }

    public function procediCalendarioPersonale(Request $request)
    {
        $request->validate([
            'corso_id'    => 'required|exists:corso,corso_id',
            'sessione_id' => 'required|exists:sessione_esami,sessione_id',
        ]);

        if (!$this->insegnamenti->haCorso($request->user(), (int) $request->corso_id)) {
            return back()->withErrors(['corso_id' => 'Non hai insegnamenti associati al corso selezionato.'])->withInput();
        }

        $sessione = Sessione::findOrFail($request->sessione_id);
        if ((int) $sessione->corso_id !== (int) $request->corso_id) {
            return back()->withErrors(['sessione_id' => 'La sessione selezionata non appartiene al corso scelto.'])->withInput();
        }

        return redirect()->route('docente.calendario', [
            'sessione_id' => $sessione->sessione_id,
            'corso_id'    => $sessione->corso_id,
        ]);
    }

    public function viewCalendarioPersonale(Request $request, int $sessione_id)
    {
        $sessione = Sessione::with('corso')->findOrFail($sessione_id);

        // La sessione appartiene a un solo corso: mostro solo gli insegnamenti del docente in quel corso
        $insegnamentiCorsi = $this->insegnamenti->associati($request->user())
            ->filter(fn ($ic) => (int) $ic->corso_id === (int) $sessione->corso_id)
            ->values();

        $insegnamentiCorsi->load(['appelli' => function ($query) use ($sessione_id) {
            $query->where('sessione_id', $sessione_id)
                ->with('aula.edificio')
                ->orderBy('data')
                ->orderBy('ora_inizio');
        }]);

        $corso = $sessione->corso;

        return view('docente.calendario_personale', compact('insegnamentiCorsi', 'sessione', 'corso'));
    }

    // ---------------------------------------------------------------- calendario generale

    public function selezionaFiltriGenerale(Request $request)
    {
        [$corsi, $sessioni] = $this->corsiESessioni($request);

        return view('docente.selezionaFiltriGenerale', compact('corsi', 'sessioni'));
    }

    /** Anni di corso in cui il docente ha insegnamenti, per il corso richiesto (JSON per il filtro). */
    public function anniCorso(Request $request, int $corso_id)
    {
        $anni = InsegnamentiCorso::where('corso_id', $corso_id)
            ->whereIn('ins_corso_id', DocenteInsegnamento::where('utente_id', $request->user()->getKey())->select('ins_corso_id'))
            ->distinct()
            ->orderBy('anno_di_corso', 'asc')
            ->pluck('anno_di_corso');

        return response()->json($anni);
    }

    public function procediCalendarioGenerale(Request $request)
    {
        $request->validate([
            'corso_id'    => 'required|exists:corso,corso_id',
            'annoCorso'   => 'required|integer|min:1',
            'sessione_id' => 'required|exists:sessione_esami,sessione_id',
        ]);

        $sessione = Sessione::findOrFail($request->sessione_id);
        if ((int) $sessione->corso_id !== (int) $request->corso_id) {
            return back()->withErrors(['sessione_id' => 'La sessione selezionata non appartiene al corso scelto.'])->withInput();
        }

        return redirect()->route('docente.calendario.generale', [
            'sessione_id' => $request->sessione_id,
            'annoCorso'   => $request->annoCorso,
            'corso_id'    => $request->corso_id,
        ]);
    }

    public function viewCalendarioGenerale(Request $request, int $sessione_id, int $annoCorso, int $corso_id)
    {
        $user = $request->user();

        $sessione = Sessione::find($sessione_id);
        $corso = Corso::find($corso_id);

        if (!$sessione || !$corso) {
            return redirect()->route('docente.selezionaFiltriGenerale')->withErrors(['msg' => 'Sessione o corso selezionato non valido.']);
        }

        // La sessione appartiene a un solo corso: deve coincidere con quello richiesto
        if ((int) $sessione->corso_id !== (int) $corso->corso_id) {
            return redirect()->route('docente.selezionaFiltriGenerale')->withErrors(['msg' => 'La sessione selezionata non appartiene al corso scelto.']);
        }

        // Il docente può vedere solo i calendari dei corsi in cui insegna (come già impone il form di selezione)
        abort_unless($this->insegnamenti->haCorso($user, $corso_id), 403, 'Non hai insegnamenti associati a questo corso.');

        // Appelli dello stesso corso, anno e sessione (in ordine cronologico)
        $appelliRaw = Appello::where('sessione_id', $sessione->sessione_id)
            ->whereHas('insegnamentoCorso', fn ($q) => $q->where('corso_id', $corso_id)->where('anno_di_corso', $annoCorso))
            ->with(['insegnamentoCorso.insegnamento', 'aula.edificio'])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        // Due sole query invece di una per ogni appello: insegnamenti del docente loggato...
        $insCorsoIds = $appelliRaw->pluck('ins_corso_id')->unique()->values();

        $mieiInsegnamenti = DocenteInsegnamento::where('utente_id', $user->getKey())
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

        return view('docente.calendario_generale', compact('corso', 'sessione', 'annoCorso', 'appelliAnnoCorso'));
    }

    /** Corsi del docente e relative sessioni (le più recenti per prime): base dei due form di selezione. */
    private function corsiESessioni(Request $request): array
    {
        $corsi = $this->insegnamenti->corsi($request->user());
        $sessioni = Sessione::whereIn('corso_id', $corsi->pluck('corso_id'))->orderBy('data_inizio', 'desc')->get();

        return [$corsi, $sessioni];
    }
}
