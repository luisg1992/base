<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\Colegio\DoctorController;
use Modules\Api\Http\Controllers\Colegio\EnfermeraController;
use Modules\Api\Http\Controllers\Colegio\ObstetraController;
use Modules\Api\Http\Controllers\Colegio\OdontologoController;
use Modules\Api\Http\Controllers\Colegio\PsicologoController;
use Modules\Api\Http\Controllers\Colegio\TecnologoController;

Route::post('/tecnologo/consultar-por-ctm', [TecnologoController::class, 'consultarPorCtm']);

Route::post('/psicologo/consultar-por-nombre', [PsicologoController::class, 'consultarPorNombre']);
Route::post('/psicologo/consultar-por-cpsp', [PsicologoController::class, 'consultarPorCpsp']);

Route::post('/odontologo/consultar-por-nombre', [OdontologoController::class, 'consultarPorNombre']);
Route::post('/odontologo/consultar-por-cop', [OdontologoController::class, 'consultarPorCop']);

Route::post('/doctor/consultar-por-nombre', [DoctorController::class, 'consultarPorNombre']);
Route::post('/doctor/consultar-por-cmp', [DoctorController::class, 'consultarPorCmp']);

Route::post('/enfermera/consultar-por-nombre', [EnfermeraController::class, 'consultarPorNombre']);
Route::post('/enfermera/consultar-por-cep', [EnfermeraController::class, 'consultarPorCep']);

Route::post('/obstetra/consultar-por-nombre', [ObstetraController::class, 'consultarPorNombre']);
Route::post('/obstetra/consultar-por-cop', [ObstetraController::class, 'consultarPorCop']);
