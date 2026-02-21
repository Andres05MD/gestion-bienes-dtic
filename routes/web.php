<?php

use App\Http\Controllers\BienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\CategoriaBienController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EstatusActaController;
use App\Http\Controllers\TransferenciaInternaController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\DesincorporacionController;
use App\Http\Controllers\DistribucionDireccionController;
use App\Http\Controllers\BienSearchController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API: Búsqueda AJAX de bienes
    Route::get('api/bienes/buscar', BienSearchController::class)->name('api.bienes.buscar');

    // Rutas para la gestión de bienes (DTIC)
    Route::match(['get', 'post'], 'bienes/import-preview', [BienController::class, 'previewImport'])->name('bienes.import-preview');
    Route::post('bienes/import', [BienController::class, 'import'])->name('bienes.import');
    Route::resource('bienes', BienController::class)
        ->parameters(['bienes' => 'bien']);

    // Rutas para la gestión de bienes externos
    Route::resource('bienes-externos', \App\Http\Controllers\BienExternoController::class)
        ->parameters(['bienes-externos' => 'bien_externo'])
        ->middleware('can:ver bienes externos');

    // Operaciones

    // Transferencias Internas
    Route::resource('transferencias-internas', TransferenciaInternaController::class)
        ->parameters(['transferencias-internas' => 'transferencias_interna'])
        ->middleware('can:ver transferencias');

    // Mantenimientos
    Route::get('mantenimientos/{mantenimiento}/devolver', [MantenimientoController::class, 'devolver'])
        ->name('mantenimientos.devolver')
        ->middleware('can:crear transferencias');
    Route::resource('mantenimientos', MantenimientoController::class)
        ->parameters(['mantenimientos' => 'mantenimiento'])
        ->middleware('can:ver transferencias');

    // Desincorporaciones
    Route::resource('desincorporaciones', DesincorporacionController::class)
        ->parameters(['desincorporaciones' => 'desincorporacione'])
        ->middleware('can:ver desincorporaciones');

    // Distribución Dirección
    Route::resource('distribuciones-direccion', DistribucionDireccionController::class)
        ->parameters(['distribuciones-direccion' => 'distribuciones_direccion'])
        ->middleware('can:ver distribuciones');

    // Rutas para la gestión de usuarios — protegidas con permiso
    Route::resource('usuarios', UserController::class)
        ->middleware('can:gestionar usuarios');

    // Resetear contraseña de un usuario — solo admin
    Route::put('usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword'])
        ->name('usuarios.reset-password')
        ->middleware('can:gestionar usuarios');

    // Historial de actividad — solo admin
    Route::get('historial-actividad', [ActivityLogController::class, 'index'])
        ->name('activity-log.index')
        ->middleware('can:gestionar usuarios');

    // Detalles (Configuración)
    Route::resource('areas', AreaController::class)->middleware('can:ver areas');
    Route::resource('estados', EstadoController::class)->middleware('can:ver estados');
    Route::resource('categorias', CategoriaBienController::class)->parameters(['categorias' => 'categoria'])->middleware('can:ver categorias');
    Route::resource('departamentos', \App\Http\Controllers\DepartamentoController::class)->middleware('can:ver departamentos');
    Route::resource('estatus-actas', EstatusActaController::class)->middleware('can:ver estatus actas');
});

require __DIR__ . '/auth.php';
