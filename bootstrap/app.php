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
        // Sessione scaduta (token CSRF non valido): per le richieste AJAX/API risposta JSON;
        // per le pagine web si torna null, così Laravel mostra resources/views/errors/4xx (stato 419).
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sessione scaduta. Ricarica la pagina e riprova.'
                ], 419);
            }

            return null;
        });
    })->create();