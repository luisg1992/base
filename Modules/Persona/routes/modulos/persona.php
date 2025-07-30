<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Persona\Http\Controllers\PersonaController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('personas/persona')
    ->middleware('auth')
    ->controller(PersonaController::class)
    ->name('personas.persona.')
    ->group(function () {

        Route::post('/PacienteBuscarTipoAndDocumento', 'PacienteBuscarTipoAndDocumento')->name('PacienteBuscarTipoAndDocumento');
        Route::post('/PacienteBuscarPorNumeroCuenta', 'PacienteBuscarPorNumeroCuenta')->name('PacienteBuscarPorNumeroCuenta');
    });
    //Route::post('personas/persona/PacienteBuscarPorNumeroCuenta', [PersonaController::class, 'PacienteBuscarPorNumeroCuenta'])->name('personas.persona.PacienteBuscarPorNumeroCuenta');