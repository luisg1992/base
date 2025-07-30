<?php

use App\Http\Controllers\Api\service_printer\consulta_externa\cita\ApiServicePrinterCita;
use App\Http\Controllers\Api\service_printer\consulta_externa\cita\ApiServicePrinterCitaFua;
use App\Http\Controllers\Api\service_printer\consulta_externa\cita\ApiServicePrinterCitaResumen;
use Illuminate\Support\Facades\Route;

Route::post('service_printer_cita', [ApiServicePrinterCita::class, 'service_printer_cita']);
Route::post('service_printer_cita_fua', [ApiServicePrinterCitaFua::class, 'service_printer_cita_fua']);
Route::post('service_printer_cita_resumen', [ApiServicePrinterCitaResumen::class, 'service_printer_cita_resumen']);
