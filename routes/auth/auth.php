<?php

use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('obtener_datos_equipo_login', [LoginController::class, 'obtener_datos_equipo_login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::get('/user-data', [ProfileController::class, 'getProfile']);
Route::post('/profile', [ProfileController::class, 'storeProfile']);
Route::post('/validate-number', [ProfileController::class, 'validateNumber']);
Route::post('/send-code', [ProfileController::class, 'sendCode']);
Route::post('/send-code-reset-password', [ProfileController::class, 'sendCodeResetPassword']);
Route::post('/reset-password', [ProfileController::class, 'storeResetPassword']);
