<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Seguridad\Http\Controllers\RolController;
use Modules\Seguridad\Http\Controllers\RoleModuleController;
use Modules\Seguridad\Http\Controllers\UserRoleModuleController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('seguridad/roles')
    ->middleware('auth')
    ->controller(RolController::class)
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
        Route::get('/listar-tablas', 'listarTablas');
    });

Route::get('seguridad/roles/modulos/records/{id?}', [RoleModuleController::class, 'getRecords']);
Route::get('seguridad/user_roles/modulos/records/{id?}', [RoleModuleController::class, 'getRecordsByUser']);
Route::get('seguridad/roles/modulos/record/{id}', [RoleModuleController::class, 'record']);
Route::post('seguridad/roles/modulos', [RoleModuleController::class, 'store']);

//Route::get('seguridad/user_roles/modulos/records/{id?}', [UserRoleModuleController::class, 'getRecords']);
