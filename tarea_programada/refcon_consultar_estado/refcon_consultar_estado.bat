@echo off
setlocal ENABLEDELAYEDEXPANSION

:: Cambiar al directorio del proyecto
cd /d C:\inetpub\wwwroot\galenweb

:: Mostrar encabezado
echo ========================================
echo Ejecutando refcon:consultar.estado
echo Fecha y hora: %date% %time%
echo ========================================
echo.

:: Ejecutar y mostrar salida directamente en consola
php artisan refcon:consultar.estado

:: Finalizar
echo.
echo Proceso finalizado.
exit
