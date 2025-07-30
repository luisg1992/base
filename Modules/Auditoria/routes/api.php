<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Api\impresion\ApiAuditoriaImpresionController;
use App\Http\Controllers\Api\tomar_datos_login\ApiTomarDatosLoginController;

 
Route::post('auditoria_impresion', [ApiAuditoriaImpresionController::class, 'auditoria_impresion']); 
Route::post('obtener_datos_equipo_login', [ApiTomarDatosLoginController::class, 'obtener_datos_equipo_login']); 
 