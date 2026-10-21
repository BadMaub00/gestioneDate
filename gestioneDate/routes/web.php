<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\docenteController;
use App\Http\Controllers\appelloController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\sessioneController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [FrontController::class, 'getLogin'])->name('login');
Route::post('/loginPerform', [FrontController::class, 'postLogin'])->name('login.perform');
Route::post('/logout', [FrontController::class, 'logout'])->name('logout');

// Rotte per il docente
Route::middleware(['auth'])->prefix('docente')->group(function () {

    // Dashboard del Docente
    Route::get('/dashboard', [docenteController::class, 'dashboard'])->name('docente.dashboard');

    // Dettaglio Insegnamento 
    Route::get('/insegnamento/{ins_corso_id}', [docenteController::class, 'dettaglioInsegnamento'])->name('docente.dettaglioInsegnamento');

    // Gestione Appelli per specifico InsegnamentoCorso
    Route::get('/insegnamento/{ins_corso_id}/appelli', [docenteController::class, 'gestioneAppelliInsegnamento'])->name('docente.appelliInsegnamento');
    
    // Calendario Personale
    Route::get('/calendario-personale/seleziona', [docenteController::class, 'selezionaSessione'])->name('docente.selezionaSessione');
    Route::post('/calendario-personale/seleziona', [docenteController::class, 'procediCalendarioPersonale'])->name('docente.procediCalendario');

    // Calendario Generale 
    Route::get('/calendario-generale/seleziona', [docenteController::class, 'selezionaFiltriGenerale'])->name('docente.selezionaFiltriGenerale');
    Route::post('/calendario-generale/seleziona', [docenteController::class, 'procedicalendario_generale'])->name('docente.procedicalendario_generale');
    Route::get('/api/anni-corso/{corso_id}', [docenteController::class, 'getAnniCorsoPerDocente'])->name('docente.api.anniCorso');

    // Visualizzazione Calendari
    Route::get('/calendario/{sessione_id}', [docenteController::class, 'viewCalendarioPersonale'])->name('docente.calendario');
    Route::get('/calendario-generale/{sessione_id}/{annoCorso}/{corso_id}', [docenteController::class, 'viewcalendarioGenerale'])
        ->whereNumber('annoCorso')
        ->name('docente.calendario.generale');
        
    // Gestione Appelli (CRUD)
    Route::get('/appelli/crea', [appelloController::class, 'create'])->name('appelli.create');
    Route::post('/appelli', [appelloController::class, 'store'])->name('appelli.store');
    Route::get('/appelli/{id}/modifica', [appelloController::class, 'edit'])->name('appelli.edit');
    Route::put('/appelli/{id}', [appelloController::class, 'update'])->name('appelli.update');
    Route::delete('/appelli/{id}', [appelloController::class, 'destroy'])->name('appelli.destroy');

});

// Rotte per l'amministratore (con AdminMiddleware applicato)
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {

    // 1. Dashboard Generale Admin
    Route::get('/dashboard', [adminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestione Corsi e Piani di Studi
    Route::get('/corsi', [adminController::class, 'indexCorsi'])->name('admin.corsi.index');
    Route::get('/corsi/crea', [adminController::class, 'createCorso'])->name('admin.corsi.create');
    Route::post('/corsi', [adminController::class, 'storeCorso'])->name('admin.corsi.store');
    Route::get('/corsi/{corso_id}/piano-di-studi', [adminController::class, 'viewPianoDiStudi'])->name('admin.corsi.pianoDiStudi');
    Route::get('/corsi/{corso_id}/insegnamenti/crea', [adminController::class, 'createInsegnamentoCorso'])->name('admin.corsi.insegnamenti.create');
    Route::post('/corsi/{corso_id}/insegnamenti', [adminController::class, 'storeInsegnamentoCorso'])->name('admin.corsi.insegnamenti.store');

    // Gestione del Personale
    Route::get('/personale', [adminController::class, 'indexPersonale'])->name('admin.personale.index');
    Route::get('/personale/crea', [adminController::class, 'createPersonale'])->name('admin.personale.create');
    Route::post('/personale', [adminController::class, 'storePersonale'])->name('admin.personale.store');

    // Rotte per Associazione Docente - Insegnamento
    Route::get('/associazioni', [adminController::class, 'indexAssociazioni'])->name('admin.personale.associazioni');
    Route::get('/personale/associa', [adminController::class, 'createAssociazioneDocente'])->name('admin.personale.associa');
    Route::post('/personale/associa', [adminController::class, 'storeAssociazioneDocente'])->name('admin.personale.storeAssocia');
    Route::delete('/associazioni/{id}', [adminController::class, 'destroyAssociazioneDocente'])->name('admin.personale.destroyAssocia');

    // 2. Calendario Globale
    Route::get('/calendario/{sessione_id?}', [adminController::class, 'viewcalendario_generale'])->name('admin.calendario');

    // Gestione Appelli lato Admin
    Route::get('/appelli/{id}/modifica', [adminController::class, 'editAppello'])->name('admin.appelli.edit');
    Route::put('/appelli/{id}', [adminController::class, 'updateAppello'])->name('admin.appelli.update');
    Route::delete('/appelli/{id}', [adminController::class, 'destroyAppello'])->name('admin.appelli.destroy');

    // Gestione degli insegnamenti
    Route::get('/insegnamenti', [adminController::class, 'indexInsegnamenti'])->name('admin.insegnamenti.index');
    Route::get('/insegnamenti/crea', [adminController::class, 'createInsegnamento'])->name('admin.insegnamenti.create');
    Route::post('/insegnamenti', [adminController::class, 'storeInsegnamento'])->name('admin.insegnamenti.store');
    Route::delete('/insegnamenti/{insegnamento_id}', [adminController::class, 'destroyInsegnamento'])->name('admin.insegnamenti.destroy');

    // 3. Gestione Sessioni d'Esame (CRUD Sessioni)
    Route::get('/sessioni', [sessioneController::class, 'index'])->name('admin.sessioni.index');
    Route::get('/sessioni/crea', [sessioneController::class, 'create'])->name('admin.sessioni.create');
    Route::post('/sessioni', [sessioneController::class, 'store'])->name('admin.sessioni.store');
    Route::get('/sessioni/{id}/modifica', [sessioneController::class, 'edit'])->name('admin.sessioni.edit');
    Route::put('/sessioni/{id}', [sessioneController::class, 'update'])->name('admin.sessioni.update');
    Route::delete('/sessioni/{id}', [sessioneController::class, 'destroy'])->name('admin.sessioni.destroy');

    // Gestione Sedi (Edifici ed Aule)
    Route::get('/sedi/edifici', [adminController::class, 'indexEdifici'])->name('admin.sedi.edifici');
    Route::get('/sedi/edifici/{edificio_id}/aule', [adminController::class, 'viewAuleEdificio'])->name('admin.sedi.aule');

});