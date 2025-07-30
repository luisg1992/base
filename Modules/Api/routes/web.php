<?php

Route::middleware('auth')
    ->group(function () {
        require __DIR__ . '/modulos/colegio.php';
        require __DIR__ . '/modulos/reniec.php';
        require __DIR__ . '/modulos/refcon.php';
        require __DIR__ . '/modulos/refcon_report.php';
        require __DIR__ . '/modulos/sis.php';
    });
