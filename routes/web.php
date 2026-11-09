<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\docenteController;
use App\Http\Controllers\appelloController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\sessioneController;
use App\Http\Controllers\CorsoController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\Docente\CalendarioController as DocenteCalendarioController;
use App\Http\Controllers\Admin\PersonaleController;
use App\Http\Controllers\Admin\AssociazioneDocenteController;
use App\Http\Controllers\Admin\InsegnamentoController;
use App\Http\Controllers\Admin\EdificioController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\AppelloAdminController;
use App\Http\Controllers\Admin\CalendarioAdminController;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DocenteMiddleware;

// Autenticazione e Login (il login è limitato a 5 tentativi al minuto per IP/utente contro il brute force)
Route::get('/', [FrontController::class, 'getLogin'])->name('login');
Route::post('/loginPerform', [FrontController::class, 'postLogin'])->middleware('throttle:5,1')->name('login.perform');
Route::post('/logout', [FrontController::class, 'logout'])->name('logout');

// ==========================================
// ROTTE DOCENTE (solo ruolo Docente)
// ==========================================
Route::middleware(['auth', DocenteMiddleware::class])->prefix('docente')->group(function () {

    Route::get('/dashboard', [docenteController::class, 'dashboard'])->name('docente.dashboard');

    // Dettaglio e gestione appelli di uno specifico InsegnamentoCorso
    Route::get('/insegnamento/{ins_corso_id}', [docenteController::class, 'dettaglioInsegnamento'])->name('docente.dettaglioInsegnamento');
    Route::get('/insegnamento/{ins_corso_id}/appelli', [docenteController::class, 'gestioneAppelliInsegnamento'])->name('docente.appelliInsegnamento');

    // Selezione e visualizzazione calendari
    Route::get('/calendario-personale/seleziona', [DocenteCalendarioController::class, 'selezionaSessione'])->name('docente.selezionaSessione');
    Route::post('/calendario-personale/seleziona', [DocenteCalendarioController::class, 'procediCalendarioPersonale'])->name('docente.procediCalendario');
    Route::get('/calendario/{sessione_id}', [DocenteCalendarioController::class, 'viewCalendarioPersonale'])->whereNumber('sessione_id')->name('docente.calendario');

    Route::get('/calendario-generale/seleziona', [DocenteCalendarioController::class, 'selezionaFiltriGenerale'])->name('docente.selezionaFiltriGenerale');
    Route::post('/calendario-generale/seleziona', [DocenteCalendarioController::class, 'procediCalendarioGenerale'])->name('docente.procedicalendario_generale');
    Route::get('/api/anni-corso/{corso_id}', [DocenteCalendarioController::class, 'anniCorso'])->whereNumber('corso_id')->name('docente.api.anniCorso');
    Route::get('/calendario-generale/{sessione_id}/{annoCorso}/{corso_id}', [DocenteCalendarioController::class, 'viewCalendarioGenerale'])
        ->whereNumber(['sessione_id', 'annoCorso', 'corso_id'])
        ->name('docente.calendario.generale');

    // Gestione Appelli (CRUD Docente, solo Primario dell'insegnamento)
    Route::get('/appelli/crea', [appelloController::class, 'create'])->name('docente.appelli.create');
    Route::post('/appelli', [appelloController::class, 'store'])->name('docente.appelli.store');
    Route::get('/appelli/{id}/modifica', [appelloController::class, 'edit'])->name('docente.appelli.edit');
    Route::put('/appelli/{id}', [appelloController::class, 'update'])->name('docente.appelli.update');
    Route::delete('/appelli/{id}', [appelloController::class, 'destroy'])->name('docente.appelli.destroy');
});

// ==========================================
// ROTTE AMMINISTRATORE (solo ruolo Amministratore)
// ==========================================
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {

    Route::get('/dashboard', [adminController::class, 'dashboard'])->name('admin.dashboard');

    // Importazione da Excel di corsi, piani di studio e associazioni docenti
    Route::get('/import', [ImportExcelController::class, 'index'])->name('admin.import.index');
    Route::get('/import/modello', [ImportExcelController::class, 'modello'])->name('admin.import.modello');
    Route::post('/import', [ImportExcelController::class, 'store'])->name('admin.import.store');

    // Corsi di Laurea e Piani di Studi
    Route::get('/corsi', [CorsoController::class, 'index'])->name('admin.corsi.index');
    Route::get('/corsi/crea', [CorsoController::class, 'create'])->name('admin.corsi.create');
    Route::post('/corsi', [CorsoController::class, 'store'])->name('admin.corsi.store');
    Route::get('/corsi/{corso_id}/piano-di-studi', [CorsoController::class, 'viewPianoDiStudi'])->name('admin.corsi.piano_di_studi');
    Route::get('/corsi/{corso_id}/modifica', [CorsoController::class, 'edit'])->name('admin.corsi.edit');
    Route::put('/corsi/{corso_id}', [CorsoController::class, 'update'])->name('admin.corsi.update');
    Route::delete('/corsi/{corso_id}', [CorsoController::class, 'destroy'])->name('admin.corsi.destroy');

    // Insegnamenti del Piano di Studi
    Route::get('/corsi/{corso_id}/insegnamenti/crea', [CorsoController::class, 'createInsegnamentoCorso'])->name('admin.corsi.insegnamenti.create');
    Route::post('/corsi/{corso_id}/insegnamenti', [CorsoController::class, 'storeInsegnamentoCorso'])->name('admin.corsi.insegnamenti.store');
    Route::delete('/corsi/insegnamenti/{id}', [CorsoController::class, 'destroyInsegnamentoCorso'])->name('admin.corsi.insegnamenti.destroy');
    Route::get('/corsi/insegnamenti/{id}/edit', [CorsoController::class, 'editInsegnamentoCorso'])->name('admin.corsi.insegnamenti.edit');
    Route::put('/corsi/insegnamenti/{id}', [CorsoController::class, 'updateInsegnamentoCorso'])->name('admin.corsi.insegnamenti.update');

    // Personale
    Route::get('/personale', [PersonaleController::class, 'index'])->name('admin.personale.index');
    Route::get('/personale/crea', [PersonaleController::class, 'create'])->name('admin.personale.create');
    Route::post('/personale', [PersonaleController::class, 'store'])->name('admin.personale.store');
    Route::delete('/personale/{id}', [PersonaleController::class, 'destroy'])->name('admin.personale.destroy');
    Route::get('/personale/{id}/modifica', [PersonaleController::class, 'edit'])->name('admin.personale.edit');
    Route::put('/personale/{id}', [PersonaleController::class, 'update'])->name('admin.personale.update');

    // Associazioni docente <-> insegnamento
    Route::get('/personale/associazioni', [AssociazioneDocenteController::class, 'index'])->name('admin.personale.associazioni_index');
    Route::get('/personale/associa', [AssociazioneDocenteController::class, 'create'])->name('admin.personale.associa');
    Route::post('/personale/associa', [AssociazioneDocenteController::class, 'store'])->name('admin.personale.storeAssocia');
    Route::delete('/personale/associazioni/{id}', [AssociazioneDocenteController::class, 'destroy'])->name('admin.personale.destroyAssocia');

    // Calendario generale appelli
    Route::get('/calendario', [CalendarioAdminController::class, 'index'])->name('admin.calendario');

    // Appelli lato Admin (modifica/eliminazione)
    Route::get('/appelli/{id}/modifica', [AppelloAdminController::class, 'edit'])->name('admin.appelli.edit');
    Route::put('/appelli/{id}', [AppelloAdminController::class, 'update'])->name('admin.appelli.update');
    Route::delete('/appelli/{id}', [AppelloAdminController::class, 'destroy'])->name('admin.appelli.destroy');

    // Insegnamenti (anagrafica)
    Route::get('/insegnamenti', [InsegnamentoController::class, 'index'])->name('admin.insegnamenti.index');
    Route::get('/insegnamenti/crea', [InsegnamentoController::class, 'create'])->name('admin.insegnamenti.create');
    Route::post('/insegnamenti', [InsegnamentoController::class, 'store'])->name('admin.insegnamenti.store');
    Route::delete('/insegnamenti/{insegnamento_id}', [InsegnamentoController::class, 'destroy'])->name('admin.insegnamenti.destroy');
    Route::get('/insegnamenti/{insegnamento_id}/modifica', [InsegnamentoController::class, 'edit'])->name('admin.insegnamenti.edit');
    Route::put('/insegnamenti/{insegnamento_id}', [InsegnamentoController::class, 'update'])->name('admin.insegnamenti.update');

    // Sessioni d'esame
    Route::get('/sessioni', [sessioneController::class, 'index'])->name('admin.sessioni.index');
    Route::get('/sessioni/crea', [sessioneController::class, 'create'])->name('admin.sessioni.create');
    Route::post('/sessioni', [sessioneController::class, 'store'])->name('admin.sessioni.store');
    Route::get('/sessioni/{id}/modifica', [sessioneController::class, 'edit'])->name('admin.sessioni.edit');
    Route::put('/sessioni/{id}', [sessioneController::class, 'update'])->name('admin.sessioni.update');
    Route::delete('/sessioni/{id}', [sessioneController::class, 'destroy'])->name('admin.sessioni.destroy');

    // Sedi: edifici e aule
    Route::get('/sedi/edifici', [EdificioController::class, 'index'])->name('admin.sedi.edifici');
    Route::get('/sedi/edifici/crea', [EdificioController::class, 'create'])->name('admin.sedi.createEdificio');
    Route::post('/sedi/edifici', [EdificioController::class, 'store'])->name('admin.sedi.storeEdificio');
    Route::get('/sedi/edifici/{id}/modifica', [EdificioController::class, 'edit'])->name('admin.sedi.editEdificio');
    Route::put('/sedi/edifici/{id}', [EdificioController::class, 'update'])->name('admin.sedi.updateEdificio');
    Route::delete('/sedi/edifici/{id}', [EdificioController::class, 'destroy'])->name('admin.sedi.destroyEdificio');

    Route::get('/sedi/edifici/{edificio_id}/aule', [AulaController::class, 'index'])->name('admin.sedi.aule');
    Route::get('/sedi/edifici/{edificio_id}/aule/crea', [AulaController::class, 'create'])->name('admin.sedi.createAula');
    Route::post('/sedi/edifici/{edificio_id}/aule', [AulaController::class, 'store'])->name('admin.sedi.storeAula');
    Route::get('/sedi/aule/{id}/modifica', [AulaController::class, 'edit'])->name('admin.sedi.editAula');
    Route::put('/sedi/aule/{id}', [AulaController::class, 'update'])->name('admin.sedi.updateAula');
    Route::delete('/sedi/aule/{id}', [AulaController::class, 'destroy'])->name('admin.sedi.destroyAula');
});
