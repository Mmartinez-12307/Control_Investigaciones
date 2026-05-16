<?php

use App\Models\Usuario;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestigacionController;
use App\Http\Controllers\DocumentoVersionController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\DocenteController;

Route::redirect('/', '/login');
Route::get('/documento/{id}', [DocumentoController::class, 'show']);
Route::get('/coordinador', [InvestigacionController::class, 'dashboard'])->middleware('auth');
Route::get('/api/dashboard', [InvestigacionController::class, 'dashboardData']);
Route::get('/investigacion/create', function () {
    $docentes = Usuario::where('IdRol', 3)->orderBy('Nombres')->get();
    return view('coordinador.create', compact('docentes'));
})->middleware('auth');
Route::post('/investigacion/{id}/estado', [InvestigacionController::class, 'updateEstado'])->middleware('auth');
Route::get('/investigaciones', [InvestigacionController::class, 'index'])->middleware('auth');
Route::get('/version/{id}', [DocumentoVersionController::class, 'show'])->middleware('auth');
Route::post('/investigacion', [InvestigacionController::class, 'store'])->middleware('auth');
Route::get('/investigacion/{id}', [InvestigacionController::class, 'show'])->middleware('auth');
Route::post('/version/{id}/estado', [DocumentoVersionController::class, 'updateEstado'])->middleware('auth');
Route::post('/version/{id}/comentario', [DocumentoVersionController::class, 'updateComentario'])->middleware('auth');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/version/{id}/estado', [CoordinadorController::class, 'actualizarEstado'])->name('coordinador.actualizar.estado');
});

require __DIR__ . '/auth.php';

// ==================== RUTAS DEL DOCENTE ====================
Route::prefix('docente')->name('docente.')->group(function () {

    Route::get('/', [DocenteController::class, 'index'])->name('index');

    Route::get('/investigaciones', [DocenteController::class, 'investigaciones'])->name('investigaciones.index');

    Route::get('/investigaciones/{id}/subir', [DocenteController::class, 'subirDocumentos'])->name('investigaciones.subir');

    Route::post('/documentos/store', [DocenteController::class, 'storeDocumento'])->name('documentos.store');

    Route::get('/investigaciones/{id}/documentos', [DocenteController::class, 'verDocumentos'])->name('investigaciones.documentos');

    Route::get('/documentos/{id}/descargar', [DocenteController::class, 'descargarDocumento'])->name('documentos.descargar');

    Route::delete('/documentos/version/{idVersion}', [DocenteController::class, 'eliminarDocumento'])->name('documentos.eliminar.version');

    Route::get('/investigaciones/{id}/revision', [DocenteController::class, 'revision'])->name('investigaciones.revision');

    Route::get('/documentos/{documentoId}/nueva-version', [DocenteController::class, 'nuevaVersionForm'])->name('documentos.nueva.version');

    Route::post('/documentos/nueva-version', [DocenteController::class, 'storeNuevaVersion'])->name('nueva.version.store');

    Route::get('/historial', [DocenteController::class, 'historial'])->name('historial');

    Route::get('/dashboard-data/{id?}', [DocenteController::class, 'dashboardData'])->name('dashboard.data');

    
});
