<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Emergencia\Http\Controllers\AdmisionEmergenciaController;
use Modules\Emergencia\Http\Controllers\TriajeAdmisionEmergenciaController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('emergencia/admision-emergencia')
    ->middleware('auth')
    ->controller(AdmisionEmergenciaController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
            Route::get('/', [AdmisionEmergenciaController::class, 'index']);
            Route::get('/inicializar_tabla', [AdmisionEmergenciaController::class, 'inicializarTabla']);
            Route::post('/actualizar_visibilidad_columnas', [AdmisionEmergenciaController::class, 'actualizarVisibilidadColumnas']);
            Route::post('/records', [AdmisionEmergenciaController::class, 'records']);

            Route::get('/triaje/inicializar_tabla', [TriajeAdmisionEmergenciaController::class, 'inicializarTabla']);
            Route::post('/triaje/actualizar_visibilidad_columnas', [TriajeAdmisionEmergenciaController::class, 'actualizarVisibilidadColumnas']);
            Route::post('/triaje/records', [TriajeAdmisionEmergenciaController::class, 'records']);
        });
        Route::get('/record/{id}', [AdmisionEmergenciaController::class, 'show']);
        Route::post('/', [AdmisionEmergenciaController::class, 'store']);
        Route::get('/record_destroy/{id}', [AdmisionEmergenciaController::class, 'recordDestroy']);
        Route::post('/destroy', [AdmisionEmergenciaController::class, 'destroy']);
        Route::get('/record_active/{id}', [AdmisionEmergenciaController::class, 'recordActive']);
        Route::post('/change_active', [AdmisionEmergenciaController::class, 'changeActive']);
        Route::post('/no-identificado', [AdmisionEmergenciaController::class, 'storeNoIdentificado']);
        Route::get('/buscar-cuentas-pendientes/{paciente_id}', [AdmisionEmergenciaController::class, 'buscarCuentasPendientes']);


    });

