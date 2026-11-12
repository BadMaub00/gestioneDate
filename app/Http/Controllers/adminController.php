<?php

namespace App\Http\Controllers;

use App\Models\Sessione;

/**
 * Solo la dashboard. Le altre funzioni dell'amministratore sono in Controllers\Admin\*
 * (Personale, AssociazioneDocente, Insegnamento, Edificio, Aula, AppelloAdmin, CalendarioAdmin)
 * più CorsoController, sessioneController e ImportExcelController.
 */
class adminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
