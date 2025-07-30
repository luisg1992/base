@echo off
setlocal ENABLEDELAYEDEXPANSION

:: Cambiar al directorio del proyecto
cd /d C:\inetpub\wwwroot\galenweb

:: Mostrar encabezado
echo ================================
echo Ejecutando refcon:recibir.cita
echo Fecha y hora: %date% %time%
echo ================================
echo.

:: Ejecutar y mostrar salida directamente en consola
php artisan refcon:recibir.cita

:: Finalizar
echo.
echo Proceso finalizado.
exit
