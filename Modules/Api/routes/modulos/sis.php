<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Sis\SisContingenciaController;
use Modules\Api\Http\Controllers\Sis\SisController;

Route::post('/sis/consultar-por-ctm', [SisController::class, 'consultarAfiliadoFuaE']);
Route::post('/sis/gonsultar_afiliaciones_temporales', [SisController::class, 'consultarAfiliacionesTemporales']);

Route::post('/sis_contingencia/consultar', [SisContingenciaController::class, 'consultarContingencia']);
