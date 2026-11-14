<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Corso;
use App\Models\Sessione;
use App\Services\CalendarioDocente;
use App\Services\InsegnamentiDocente;
use Illuminate\Http\Request;

/** Calendari (personale e generale) e relativi filtri, visti dal docente loggato. */
class CalendarioController extends Controller
{
    public function __construct(private InsegnamentiDocente $insegnamenti, private CalendarioDocente $calendario)
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
        if (!$sessione->appartieneACorso((int) $request->corso_id)) {
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
        $insegnamentiCorsi = $this->calendario->personale($request->user(), $sessione);

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
        $anni = $this->insegnamenti->anniCorso($request->user(), $corso_id);

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
        if (!$sessione->appartieneACorso((int) $request->corso_id)) {
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
        if (!$sessione->appartieneACorso((int) $corso->corso_id)) {
            return redirect()->route('docente.selezionaFiltriGenerale')->withErrors(['msg' => 'La sessione selezionata non appartiene al corso scelto.']);
        }

        // Il docente può vedere solo i calendari dei corsi in cui insegna (come già impone il form di selezione)
        abort_unless($this->insegnamenti->haCorso($user, $corso_id), 403, 'Non hai insegnamenti associati a questo corso.');

        $appelliAnnoCorso = $this->calendario->generale($user, $sessione, $corso_id, $annoCorso);

        return view('docente.calendario_generale', compact('corso', 'sessione', 'annoCorso', 'appelliAnnoCorso'));
    }

    /** Corsi del docente e relative sessioni (le più recenti per prime): base dei due form di selezione. */
    private function corsiESessioni(Request $request): array
    {
        $docente = $request->user();

        return [$this->insegnamenti->corsi($docente), $this->insegnamenti->sessioni($docente)];
    }
}
