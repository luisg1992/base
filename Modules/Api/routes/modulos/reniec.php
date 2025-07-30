<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Reniec\ReniecAvanzadoController;
use Modules\Api\Http\Controllers\Reniec\ReniecBasicoController;

Route::post('/reniec/basico/consultar', [ReniecBasicoController::class, 'consultar']);
Route::post('/reniec/avanzado/consultar', [ReniecAvanzadoController::class, 'consultar']);
