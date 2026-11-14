<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Services\InsegnamentiDocente;
use Illuminate\Http\Request;

/** Dashboard del docente loggato. */
class DashboardController extends Controller
{
    public function __construct(private InsegnamentiDocente $insegnamenti)
    {
    }

    public function dashboard(Request $request)
    {
        $insegnamentiCorsi = $this->insegnamenti->associati($request->user());

        return view('docente.dashboard', compact('insegnamentiCorsi'));
    }
}
