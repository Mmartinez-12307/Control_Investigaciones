<?php
use App\Models\Usuario;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestigacionController;
use App\Http\Controllers\DocumentoVersionController;
use App\Http\Controllers\DocumentoController;

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
});

require __DIR__.'/auth.php';
