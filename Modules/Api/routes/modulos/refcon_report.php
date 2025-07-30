<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\RefconReport\RefconReportController;

Route::group([
    'prefix' => 'refcon_report',
    'controller' => RefconReportController::class
], function () {
    Route::post('/login', 'login');
    Route::post('/preview_referencia', 'previewReferencia'); 
    Route::post('/recibir_paciente_refcon', 'recibirPacienteRefcon'); 
    Route::post('/listar_pacientes_referidos', 'listarPacientesReferidos');
});

