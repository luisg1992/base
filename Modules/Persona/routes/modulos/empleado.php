<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Persona\Http\Controllers\EmpleadoController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('seguridad/empleados')
    ->middleware('auth')
    ->controller(EmpleadoController::class)
    ->as($as)
    ->group(function () use ($as) {
//        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
            Route::get('/', 'index');
            Route::get('/inicializar_tabla', 'inicializarTabla');
            Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
            Route::post('/records', 'records');
//        });
        Route::get('/record/{id}', 'show');
        Route::post('/', 'store');
        Route::get('/record_destroy/{id}', 'recordDestroy');
        Route::post('/destroy', 'destroy');
        Route::get('/record_active/{id}', 'recordActive');
        Route::post('/change_active', 'changeActive');
        Route::post('/actualizar_empleado_por_reniec', 'actualizarEmpleadoPorReniec');

        Route::post('/generar_usuario', 'generarUsuario');
        Route::post('/eliminar_usuario', 'eliminarUsuario');
        Route::post('/consultar-colegiatura', 'consultarColegiatura');
    });
