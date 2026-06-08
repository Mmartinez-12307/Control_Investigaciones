<?php

use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvestigacionController;
use App\Http\Controllers\DocumentoVersionController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\DecanoController;

Route::redirect('/', '/login');

Route::get('/documento/{id}', [DocumentoController::class, 'show']);

Route::middleware('auth')->group(function () {

    Route::get('/coordinador', [InvestigacionController::class, 'dashboard']);
    Route::get('/api/dashboard', [InvestigacionController::class, 'dashboardData']);

    Route::get('/investigacion/create', function () {
        $docentes = Usuario::where('IdRol', 3)->orderBy('Nombres')->get();
        return view('coordinador.create', compact('docentes'));
    });

    Route::post('/investigacion/{id}/estado', [InvestigacionController::class, 'updateEstado']);
    Route::get('/investigaciones', [InvestigacionController::class, 'index']);
    Route::get('/version/{id}', [DocumentoVersionController::class, 'show']);
    Route::post('/investigacion', [InvestigacionController::class, 'store']);
    Route::get('/investigacion/{id}', [InvestigacionController::class, 'show']);
    Route::post('/version/{id}/estado', [DocumentoVersionController::class, 'updateEstado']);
    Route::post('/version/{id}/comentario', [DocumentoVersionController::class, 'updateComentario']);

    Route::post('/version/{id}/estado', [CoordinadorController::class, 'actualizarEstado'])
        ->name('coordinador.actualizar.estado');

    Route::get('/decano', [DecanoController::class, 'index'])
        ->name('decano.index');

    Route::get('/decano/usuarios', [DecanoController::class, 'usuarios'])
        ->name('decano.usuarios');

    Route::post('/decano/usuarios', [DecanoController::class, 'storeUsuario'])
        ->name('decano.usuarios.store');

    Route::put('/decano/usuarios/{id}', [DecanoController::class, 'updateUsuario'])
        ->name('decano.usuarios.update');

    Route::get('/decano/escuela/{id}', [DecanoController::class, 'escuela']);
    Route::get('/decano/investigacion/{id}', [DecanoController::class, 'showInvestigacion']);
    Route::get('/decano/documento/{id}', [DecanoController::class, 'showDocumento']);
    Route::get('/decano/version/{id}', [DecanoController::class, 'showVersion']);

    Route::get('/decano/reportes/excel', [DecanoController::class, 'exportExcel'])
        ->name('decano.reportes.excel');

    Route::get('/decano/reportes/pdf', [DecanoController::class, 'exportPdf'])
        ->name('decano.reportes.pdf');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::prefix('docente')
    ->name('docente.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/', [DocenteController::class, 'index'])->name('index');

        Route::get('/investigaciones', [DocenteController::class, 'investigaciones'])
            ->name('investigaciones.index');

        Route::get('/investigaciones/{id}/subir', [DocenteController::class, 'subirDocumentos'])
            ->name('investigaciones.subir');

        Route::post('/documentos/store', [DocenteController::class, 'storeDocumento'])
            ->name('documentos.store');

        Route::get('/investigaciones/{id}/documentos', [DocenteController::class, 'verDocumentos'])
            ->name('investigaciones.documentos');

        Route::get('/documentos/{id}/descargar', [DocenteController::class, 'descargarDocumento'])
            ->name('documentos.descargar');

        Route::delete('/documentos/version/{idVersion}', [DocenteController::class, 'eliminarDocumento'])
            ->name('documentos.eliminar.version');

        Route::get('/investigaciones/{id}/revision', [DocenteController::class, 'revision'])
            ->name('investigaciones.revision');

        Route::get('/documentos/{documentoId}/nueva-version', [DocenteController::class, 'nuevaVersionForm'])
            ->name('documentos.nueva.version');

        Route::post('/documentos/nueva-version', [DocenteController::class, 'storeNuevaVersion'])
            ->name('nueva.version.store');

        Route::get('/historial', [DocenteController::class, 'historial'])
            ->name('historial');

        Route::get('/dashboard-data/{id?}', [DocenteController::class, 'dashboardData'])
            ->name('dashboard.data');
    });