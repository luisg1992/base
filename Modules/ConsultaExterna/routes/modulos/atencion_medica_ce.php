<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\ConsultaExterna\Http\Controllers\AtencionMedicaCEController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('consulta-externa/atencion-medica')
    ->middleware('auth')
    ->controller(AtencionMedicaCEController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
            Route::get('/', 'index');
            Route::get('/inicializar_tabla', 'inicializarTabla');
            Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
            Route::post('/records', 'records');
            Route::post('/servicios/programados/por-fecha', 'obtenerServiciosProgramados');
        });
        Route::get('/record/{id}', 'show'); 
    });
