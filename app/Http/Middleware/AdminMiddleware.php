<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Controlla il campo del ruolo (es. 'admin', 'AMMINISTRATORE', 'ruolo' == 1, ecc.)
        if (Auth::check() && Auth::user()->ruolo === 'Amministratore') { 
            return $next($request);
        }

        // Se non è admin, blocca l'accesso
        abort(403, 'ACCESSO NEGATO: SEZIONE RISERVATA AGLI AMMINISTRATORI.');
    }
}