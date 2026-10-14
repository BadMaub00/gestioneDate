<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\docenteController;
use App\Http\Controllers\appelloController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\sessioneController;

/*Route::get('/', function () {
    return view('welcome');
});*/

/*Route::post('/login', function () {
    return "Form inviato con successo!";
});*/

Route::get('/', [frontController::class, 'getLogin'])->name('login');
Route::post('/loginPerform', [frontController::class, 'postLogin'])->name('login.perform');
Route::post('/logout', [frontController::class, 'logout'])->name('logout');

//routes per il docente
Route::middleware(['auth'])->prefix('docente')->group(function () {

    // 1. Dashboard del Docente
    Route::get('/dashboard', [docenteController::class, 'dashboard'])->name('docente.dashboard');

    // 2. Calendario Personale del Docente
    Route::get('/docente/dashboard', [DocenteController::class, 'index'])->name('docente.dashboard');
    Route::get('/calendario/{sessione_id}', [docenteController::class, 'viewCalendarioPersonale'])->name('docente.calendario');

    // 2.1 Calendario Generale del Docente
    Route::get('/calendario-generale/{sessione_id}/{annoCorso}/{corso_id}', [docenteController::class, 'viewCalendarioGenerale'])
        ->whereNumber('annoCorso')
        ->name('docente.calendario.generale');
        
    // 3. Creazione Appello
    Route::get('/appelli/crea', [appelloController::class, 'create'])->name('appelli.create');
    Route::post('/appelli', [appelloController::class, 'store'])->name('appelli.store');

    // 4. Form di Modifica Appello (GET)
    Route::get('/appelli/{id}/modifica', [appelloController::class, 'edit'])->name('appelli.edit');

    // 5. Salvataggio Modifica Appello (PUT/PATCH)
    Route::put('/appelli/{id}', [appelloController::class, 'update'])->name('appelli.update');

    // 6. Cancellazione Appello (DELETE)
    Route::delete('/appelli/{id}', [appelloController::class, 'destroy'])->name('appelli.destroy');

});

//routes per l'amministratore
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // 1. Dashboard Generale Admin
    Route::get('/dashboard', [adminController::class, 'dashboard'])->name('admin.dashboard');

    // 2. Calendario Globale (vede gli appelli di tutti i docenti e corsi)
    Route::get('/calendario/{sessione_id?}', [adminController::class, 'viewCalendarioGenerale'])->name('admin.calendario');

    // 3. Gestione Sessioni d'Esame (CRUD Sessioni)
    Route::get('/sessioni', [sessioneController::class, 'index'])->name('sessioni.index');
    Route::get('/sessioni/crea', [sessioneController::class, 'create'])->name('sessioni.create');
    Route::post('/sessioni', [sessioneController::class, 'store'])->name('sessioni.store');
    Route::get('/sessioni/{id}/modifica', [sessioneController::class, 'edit'])->name('sessioni.edit');
    Route::put('/sessioni/{id}', [sessioneController::class, 'update'])->name('sessioni.update');
    Route::delete('/sessioni/{id}', [sessioneController::class, 'destroy'])->name('sessioni.destroy');

});




