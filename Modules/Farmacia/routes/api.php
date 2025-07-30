<?php

use Illuminate\Support\Facades\Route;
use Modules\Farmacia\Http\Controllers\FarmaciaAlmacenController;
use Modules\Farmacia\Http\Controllers\FarmaciaController;
use Modules\Farmacia\Http\Controllers\NotaIngresoAlmacenController;
use Modules\Farmacia\Http\Controllers\NotaIngresoFarmaciaController;
use Modules\Farmacia\Http\Controllers\NotaSalidaAlmacenController;

Route::middleware(['auth:sanctum'])
    ->group(function () {
        require __DIR__ . '/route.php';
    });
