<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Emergencia\Http\Controllers\TriajeEmergenciaController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('emergencia/triaje-emergencia')
    ->middleware('auth')
    ->controller(TriajeEmergenciaController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
            Route::get('/', 'index');
            Route::get('/inicializar_tabla', 'inicializarTabla');
            Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
            Route::post('/records', 'records');
        });
        Route::get('/record/{id}', 'show');
        Route::post('/', 'store');
        Route::get('/record_destroy/{id}', 'recordDestroy');
        Route::post('/destroy', 'destroy');
        Route::get('/record_active/{id}', 'recordActive');
        Route::post('/change_active', 'changeActive');
        Route::get('/validar_triaje_emergencia_paciente/{paciente_id}', 'validarTriajeEmergenciaPaciente');
        Route::post('/validar_triaje_emergencia_paciente', 'validarTriajeEmergenciaPacientePorDocumento');
    });
