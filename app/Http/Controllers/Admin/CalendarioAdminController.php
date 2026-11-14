<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Corso;
use App\Models\Sessione;
use App\Services\CalendarioAdmin;
use Illuminate\Http\Request;

/** Calendario generale degli appelli visto dall'amministratore. */
class CalendarioAdminController extends Controller
{
    public function __construct(private CalendarioAdmin $calendario)
    {
    }

    public function index(Request $request)
    {
        $anno_di_corso = $request->input('anno_di_corso');

        // Sessioni in ordine di data di inizio (ognuna appartiene a un corso)
        $sessioni = Sessione::orderBy('data_inizio', 'asc')->get();

        [$sessione_id, $corso_id] = $this->calendario->normalizzaFiltri(
            $request->input('sessione_id'),
            $request->input('corso_id'),
            $sessioni
        );

        $corsi = Corso::orderBy('nome', 'asc')->get();
        $appelliGrouped = $this->calendario->appelliRaggruppati($sessione_id, $corso_id, $anno_di_corso);

        return view('admin.calendario', compact('appelliGrouped', 'sessioni', 'corsi', 'sessione_id', 'corso_id', 'anno_di_corso'));
    }
}
