<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Configuracion\Http\Controllers\ServicioController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('configuracion/servicios')
    ->middleware('auth')
    ->controller(ServicioController::class)
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
        Route::post('/duplicar', 'duplicar')->name('duplicar');

        Route::post('/filtrar_catalogo_emergencias', 'filtrarCatalogoEmergencias')->name('filtrar_catalogo_emergencias');
        Route::get('/filtrar_catalogo_emergencia_por_codigo/{codigo}', 'filtrarCatalogoEmergenciaPorCodigo')->name('filtrar_catalogo_emergencia_por_codigo');
        Route::get('/listar_ups_adicionales/{codigo}', 'listarUpsAdicionales')->name('listar_ups_adicionales');
        Route::get('/ups_adicionales/{codigo}', 'upsAdicionales')->name('ups_adicionales');
        Route::post('/ups_adicional_agregar', 'agregarUpsAdicional')->name('ups_adicional_agregar');
        Route::post('/ups_adicional_eliminar', 'eliminarUpsAdicional')->name('ups_adicional_eliminar');
    });
