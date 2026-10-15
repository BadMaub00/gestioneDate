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
// Routes per il docente
// Routes per il docente
Route::middleware(['auth'])->prefix('docente')->group(function () {

    // 1. Dashboard del Docente
    Route::get('/dashboard', [docenteController::class, 'dashboard'])->name('docente.dashboard');

    Route::get('/calendario-personale/seleziona', [docenteController::class, 'selezionaSessione'])->name('docente.selezionaSessione');
    Route::post('/calendario-personale/seleziona', [docenteController::class, 'procediCalendarioPersonale'])->name('docente.procediCalendario');

    // Calendario Generale (Nuove Rotte)
    Route::get('/calendario-generale/seleziona', [docenteController::class, 'selezionaFiltriGenerale'])->name('docente.selezionaFiltriGenerale');
    Route::post('/calendario-generale/seleziona', [docenteController::class, 'procedicalendario_generale'])->name('docente.procedicalendario_generale');
    Route::get('/api/anni-corso/{corso_id}', [docenteController::class, 'getAnniCorsoPerDocente'])->name('docente.api.anniCorso');

    // Visualizzazione Calendari
    Route::get('/calendario/{sessione_id}', [docenteController::class, 'viewCalendarioPersonale'])->name('docente.calendario');
    Route::get('/calendario-generale/{sessione_id}/{annoCorso}/{corso_id}', [docenteController::class, 'viewcalendario_generale'])
    ->whereNumber('annoCorso')
    ->name('docente.calendario.generale');
        
    // 4. Gestione Appelli (CRUD)
    Route::get('/appelli/crea', [appelloController::class, 'create'])->name('appelli.create');
    Route::post('/appelli', [appelloController::class, 'store'])->name('appelli.store');
    Route::get('/appelli/{id}/modifica', [appelloController::class, 'edit'])->name('appelli.edit');
    Route::put('/appelli/{id}', [appelloController::class, 'update'])->name('appelli.update');
    Route::delete('/appelli/{id}', [appelloController::class, 'destroy'])->name('appelli.destroy');

});

//routes per l'amministratore
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // 1. Dashboard Generale Admin
    Route::get('/dashboard', [adminController::class, 'dashboard'])->name('admin.dashboard');
    
    Route::get('/corsi', [adminController::class, 'indexCorsi'])->name('admin.corsi.index');
    Route::get('/corsi/{corso_id}/piano-di-studi', [adminController::class, 'viewPianoDiStudi'])->name('admin.corsi.pianoDiStudi');
    
    // 2. Calendario Globale (vede gli appelli di tutti i docenti e corsi)
    Route::get('/calendario/{sessione_id?}', [adminController::class, 'viewcalendario_generale'])->name('admin.calendario');

    // 3. Gestione Sessioni d'Esame (CRUD Sessioni)
    Route::get('/sessioni', [sessioneController::class, 'index'])->name('sessioni.index');
    Route::get('/sessioni/crea', [sessioneController::class, 'create'])->name('sessioni.create');
    Route::post('/sessioni', [sessioneController::class, 'store'])->name('sessioni.store');
    Route::get('/sessioni/{id}/modifica', [sessioneController::class, 'edit'])->name('sessioni.edit');
    Route::put('/sessioni/{id}', [sessioneController::class, 'update'])->name('sessioni.update');
    Route::delete('/sessioni/{id}', [sessioneController::class, 'destroy'])->name('sessioni.destroy');

});




