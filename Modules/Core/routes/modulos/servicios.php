<?php

use App\Http\Controllers\ws\WsController;
use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Refcon\ApiRefconController;
use Modules\Api\Http\Controllers\Refcon\ListadoEspecialidadesRefcon;
use Modules\Api\Http\Controllers\Refcon\ListadoUpsRefcon;
use Modules\Api\Http\Controllers\Refcon\ListarPacienteReferidosController;
use Modules\Api\Http\Controllers\RefconReport\RefconPreviewPDFController;
use Modules\Api\Http\Controllers\RefconMicroservicios\ConsultaBandejaReferidosRecibidosRefconController;

Route::prefix('core/servicios')
    ->middleware('auth')
    ->controller(WsController::class)
    ->name('core.servicios.')
    ->group(function () {
        Route::post('obtenerDatosSisCompletos', 'obtenerDatosSisCompletos');
        Route::post('obtenerDatosFiliacionSis', 'obtenerDatosFiliacionSis');
        Route::post('obtenerDatosReniecCompletos', 'obtenerDatosReniecCompletos');
    });

Route::prefix('core/servicios')
    ->middleware('auth')
    ->controller(ApiRefconController::class)
    ->name('core.servicios.')
    ->group(function () {
        Route::post('consultaReferenciaPaciente', 'consultaReferenciaPaciente');
    });

Route::prefix('core/servicios')
    ->middleware('auth')
    ->controller(RefconPreviewPDFController::class)
    ->name('core.servicios.')
    ->group(function () {
        Route::post('visualizarReferenciaRefcon', 'visualizarReferenciaRefcon');
    });

Route::prefix('core/servicios')
    ->middleware('auth')
    ->controller(ListarPacienteReferidosController::class)
    ->name('core.servicios.')
    ->group(function () {
        Route::post('RefConListarPacienteReferidos', 'RefConListarPacienteReferidos');
        Route::get('RefConListarPacienteReferidosExportar', 'RefConListarPacienteReferidosExportar');
    });


Route::prefix('microservicios/referencias-aceptadas')
    ->middleware('auth')
    ->controller(ConsultaBandejaReferidosRecibidosRefconController::class)
    ->name('microservicios.referencias.aceptadas')
    ->group(function () {
        Route::post('obtenerRegistrosReferencias', 'obtenerRegistrosReferencias');
    });


Route::prefix('refcon')->middleware('auth')->group(function () {
    Route::controller(ListadoUpsRefcon::class)->group(function () {
        Route::post('listadoUps', 'listadoUps')->name('servicios.refcon.listadoups');
    });

    Route::controller(ListadoEspecialidadesRefcon::class)->group(function () {
        Route::post('listadoEspecialidades', 'listadoEspecialidades')->name('servicios.refcon.listadoespecialidades');
    });
});
