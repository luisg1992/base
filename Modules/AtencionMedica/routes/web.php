<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaAnamnesisController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaDiagnosticoController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaFarmaciaController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaInterconsultaController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaProcedimientoController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaRecetaController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaAnamnesisController::class)
    // ->as($as)
    ->group(function () use ($as) {
        // CONTROL DE ANMANESIS
        Route::post('/WebS_InsertarAnamnesisAtencion', 'WebS_InsertarAnamnesisAtencion');
    });



Route::prefix('atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaDiagnosticoController::class)
    // ->as($as)
    ->group(function () use ($as) {
        // DIAGNÓSTICOS DE ATENCIÓN MÉDICA
        Route::post('/WebS_InsertarDiagnosticoAtencion', 'WebS_InsertarDiagnosticoAtencion');
        Route::post('/WebS_EliminarDiagnosticoAtencion', 'WebS_EliminarDiagnosticoAtencion');
        Route::post('/WebS_ListarDiagnosticosAtencion', 'WebS_ListarDiagnosticosAtencion');
    });



Route::prefix('atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaRecetaController::class)
    // ->as($as)
    ->group(function () use ($as) {
        // CONTROL DE RECETAS (FARMACIA Y APOYO AL DX)
        Route::post('/WebS_RecetaCabeceraAgregar', 'WebS_RecetaCabeceraAgregar');
        Route::post('/WebS_RecetaDetalleAgregar', 'WebS_RecetaDetalleAgregar');
        Route::post('/WebS_RecetaDetalle_Eliminar', 'WebS_RecetaDetalle_Eliminar');
    });



Route::prefix('atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaFarmaciaController::class)
    // ->as($as)
    ->group(function () use ($as) {
        // CONTROL DE FARMACIA
        Route::post('/WebS_ListarDetalleRecetaAtencion', 'WebS_ListarDetalleRecetaAtencion');
    });



Route::prefix('atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaProcedimientoController::class)
    // ->as($as)
    ->group(function () use ($as) {
        // CONTROL DE RECETAS PROCEDIMIENTOS
        Route::post('/WebS_InsertarOrdenServicio', 'WebS_InsertarOrdenServicio');
        Route::post('/WebS_ModificarOrdenServicio', 'WebS_ModificarOrdenServicio');
        Route::post('/WebS_EliminarOrdenServicio', 'WebS_EliminarOrdenServicio');
    });



Route::prefix('atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaInterconsultaController::class)
    // ->as($as)
    ->group(function () use ($as) {
        // CONTROL DE INTERSONAULTAS
        Route::post('/WebS_Atenciones_Interconsulta_Insertar', 'WebS_Atenciones_Interconsulta_Insertar');
        Route::post('/WebS_Atenciones_Interconsulta_Eliminar', 'WebS_Atenciones_Interconsulta_Eliminar');
    });
