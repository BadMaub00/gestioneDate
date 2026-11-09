<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Speculare ad AdminMiddleware: le rotte /docente/* sono riservate ai soli docenti,
 * così un amministratore non può usare (nemmeno digitando l'URL) le funzioni dei docenti.
 */
class DocenteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->isDocente()) {
            return $next($request);
        }

        abort(403, 'ACCESSO NEGATO: SEZIONE RISERVATA AI DOCENTI.');
    }
}
