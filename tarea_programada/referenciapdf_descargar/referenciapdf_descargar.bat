@echo off
setlocal ENABLEDELAYEDEXPANSION

:: Cambiar al directorio del proyecto
cd /d C:\inetpub\wwwroot\galenweb

:: Mostrar encabezado
echo ========================================
echo Ejecutando refcon:descargar.pdf
echo Fecha y hora: %date% %time%
echo ========================================
echo.

:: Ejecutar y mostrar salida directamente en consola
php artisan refcon:descargar.pdf

:: Finalizar
echo.
echo Proceso finalizado.
exit
