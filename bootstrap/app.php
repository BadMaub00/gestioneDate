<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Qui puoi registrare middleware personalizzati o modificarne il comportamento
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            // Se la richiesta è via AJAX/API (si aspetta JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sessione scaduta. Ricarica la pagina e riprova.'
                ], 419);
            }

            // Per richieste web tradizionali, reindirizza alla rotta 'login'
            return redirect()
                ->route('login')
                ->with('warning', 'La tua sessione è scaduta. Effettua nuovamente il login.');
        });
    })->create();