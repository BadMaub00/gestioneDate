<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\DocenteInsegnamento;
use App\Services\InsegnamentiDocente;
use Illuminate\Http\Request;

/** Pagine di un singolo insegnamento del docente loggato (dettaglio e appelli per sessione). */
class InsegnamentoController extends Controller
{
    public function __construct(private InsegnamentiDocente $insegnamenti)
    {
    }

    public function dettaglioInsegnamento(Request $request, int $ins_corso_id)
    {
        $user = $request->user();

        $insegnamentoCorso = $this->insegnamenti->trovaOppure404($user, $ins_corso_id);
        $docenteInsegnamento = $this->insegnamenti->incarico($user, $ins_corso_id);

        // Tutti i docenti dell'insegnamento in una sola query, poi divisi per ruolo
        $docenti = DocenteInsegnamento::docentiConDati($ins_corso_id);

        $docentePrimario = $docenti->firstWhere('ruolo', DocenteInsegnamento::RUOLO_PRIMARIO);
        $docentiSecondari = $docenti->where('ruolo', DocenteInsegnamento::RUOLO_SECONDARIO)->values();

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

        // Solo le sessioni del corso dell'insegnamento, ognuna con i suoi appelli
        $sessioni = $insegnamentoCorso->sessioniConAppelli();

        return view('docente.appello_form', compact('insegnamentoCorso', 'docenteInsegnamento', 'sessioni'));
    }
}
