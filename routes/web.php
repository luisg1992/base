<?php

use App\Http\Controllers\CacheController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/obtener_cache', [CacheController::class, 'all']);
    Route::get('/obtener_tablas/{tabla?}', [AppController::class, 'getTables'])->name('obtener_tablas');
    Route::get('/obtener_user_login', [AppController::class, 'getUserLogin'])->name('obtener_user_login');

    Route::post('/filtrar_persona_empleados', [AppController::class, 'filtrarEmpleados'])->name('filtrar_persona_empleados');
    Route::post('/filtrar_catalogo_establecimientos', [AppController::class, 'filtrarEstablecimientos'])->name('filtrar_establecimientos');
    Route::post('/filtrar_catalogo_servicio_especialidades', [AppController::class, 'filtrarServicioEspecialidades'])->name('filtrar_catalogo_servicio_especialidades');
    Route::post('/filtrar_configuracion_lugares_laborales_por_tipo', [AppController::class, 'filtrarLugaresLaboralesPorTipo'])->name('filtrar_configuracion_lugares_laborales_por_tipo');
    Route::post('/filtrar_configuracion_centros_poblados', [AppController::class, 'filtrarCentrosPoblados'])->name('filtrar_configuracion_centros_poblados');


    Route::post('/filtrar_diagnosticos', [AppController::class, 'filtrarDiagnosticos'])->name('filtrar_diagnosticos');

    Route::post('/filtrar_cpt', [AppController::class, 'filtrarCPT'])->name('filtrar_cpt');
    Route::post('/filtrar_apoyo_dx', [AppController::class, 'filtrarApoyoDX'])->name('filtrar_apoyo_dx');
    Route::post('/filtrar_producto_farmacia', [AppController::class, 'filtrarProductosFarmacia'])->name('filtrar_producto_farmacia');

    Route::post('/filtrar_almanacenes_por_tipo_de_conceptos', [AppController::class, 'filtrarAlmanacenesPorTipoDeConceptos']);

    Route::post('/filtrar_establecimientos', [AppController::class, 'filtrarEstablecimientosPorNombre']);
});


// Rutas de autenticación
require __DIR__ . '/auth/auth.php';
