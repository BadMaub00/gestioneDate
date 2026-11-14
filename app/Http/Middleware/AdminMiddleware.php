<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Le rotte /admin/* sono riservate ai soli amministratori.
 * Speculare a DocenteMiddleware: il ruolo si controlla tramite il modello (Utente::isAdmin()).
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        abort(403, 'ACCESSO NEGATO: SEZIONE RISERVATA AGLI AMMINISTRATORI.');
    }
}