<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Persona\Http\Controllers\PacienteController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('personas/pacientes')
    ->middleware('auth')
    ->controller(PacienteController::class)
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
        Route::post('/actualizar_paciente_por_reniec', 'actualizarPacientePorReniec');
    });

Route::prefix('personas/pacientes')
    ->middleware('auth')
    ->controller(PacienteController::class)
    ->group(function () {
        Route::post('/WebS_Pacientes_BuscarFiltro', 'WebS_Pacientes_BuscarFiltro');
    });
