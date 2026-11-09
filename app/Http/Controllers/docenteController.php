<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use App\Models\DocenteInsegnamento;
use App\Models\Sessione;
use App\Services\InsegnamentiDocente;
use Illuminate\Http\Request;

/**
 * Dashboard e pagine degli insegnamenti del docente loggato.
 * I calendari sono in Docente\CalendarioController.
 */
class docenteController extends Controller
{
    public function __construct(private InsegnamentiDocente $insegnamenti)
    {
    }

    public function dashboard(Request $request)
    {
        $insegnamentiCorsi = $this->insegnamenti->associati($request->user());

        return view('docente.dashboard', compact('insegnamentiCorsi'));
    }

    public function dettaglioInsegnamento(Request $request, int $ins_corso_id)
    {
        $user = $request->user();

        $insegnamentoCorso = $this->insegnamenti->trovaOppure404($user, $ins_corso_id);
        $docenteInsegnamento = $this->insegnamenti->incarico($user, $ins_corso_id);

        // Tutti i docenti dell'insegnamento in una sola query, poi divisi per ruolo
        $docenti = DocenteInsegnamento::where('docente_insegnamento.ins_corso_id', $ins_corso_id)
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->select('utente.utente_id', 'utente.nome', 'utente.cognome', 'docente_insegnamento.ruolo')
            ->get();

        $docentePrimario = $docenti->firstWhere('ruolo', 'Primario');
        $docentiSecondari = $docenti->where('ruolo', 'Secondario')->values();

        return view('docente.dettaglio_insegnamento', compact(
            'insegnamentoCorso',
            'docenteInsegnamento',
            'docentePrimario',
            'docentiSecondari'
        ));
    }

    public function gestioneAppelliInsegnamento(Request $request, int $ins_corso_id)
    {
        $user = $request->user();

        $insegnamentoCorso = $this->insegnamenti->trovaOppure404($user, $ins_corso_id);
        $docenteInsegnamento = $this->insegnamenti->incarico($user, $ins_corso_id);

        // Solo le sessioni del corso a cui appartiene l'insegnamento
        $sessioni = Sessione::where('corso_id', $insegnamentoCorso->corso_id)->orderBy('data_inizio', 'asc')->get();

        // Una sola query per tutti gli appelli, poi distribuiti per sessione
        $appelliPerSessione = Appello::where('ins_corso_id', $ins_corso_id)
            ->whereIn('sessione_id', $sessioni->pluck('sessione_id'))
            ->with('aula.edificio')
            ->orderBy('data', 'asc')
            ->orderBy('ora_inizio', 'asc')
            ->get()
            ->groupBy('sessione_id');

        foreach ($sessioni as $sessione) {
            $sessione->appelli_insegnamento = $appelliPerSessione->get($sessione->sessione_id, collect());
        }

        return view('docente.appello_form', compact('insegnamentoCorso', 'docenteInsegnamento', 'sessioni'));
    }
}
