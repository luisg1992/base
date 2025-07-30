<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\ConsultaExterna\Http\Controllers\CitasController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();
Route::prefix('consulta-externa/citas')
    ->middleware('auth')
    ->controller(CitasController::class)
    ->as($as)
    ->name($as . '.citas.')
    ->group(function () use ($as) {

        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
            Route::get('/', 'index')->name('index');;
        });

        Route::post('/WebS_ProgramacionMedica_Servicios_BuscarFiltro', 'WebS_ProgramacionMedica_Servicios_BuscarFiltro');

        Route::post('/WebS_CitasCupos_BuscarFiltro', 'WebS_CitasCupos_BuscarFiltro');
        Route::post('/WebS_ProgramacionMedica_Lista_BuscarFiltro', 'WebS_ProgramacionMedica_Lista_BuscarFiltro');

        //GENERAR PDF DE CITA
        Route::post('/CitasPrinter', 'CitasPrinter');
        Route::post('/CitasBuscarIdCitaFormatoPDF', 'CitasBuscarIdCitaFormatoPDF');
        Route::post('/WebS_InsertarCita', 'WebS_InsertarCita');
        Route::post('/WebS_InsertarCita_Interconsulta_CitaControl', 'WebS_InsertarCita_Interconsulta_CitaControl');

        Route::post('/CitasFuaAdmisionPrinter', 'CitasFuaAdmisionPrinter');
        Route::post('/WebS_GenerarFormatoFua', 'WebS_GenerarFormatoFua');
        Route::post('/WebS_AdmisionCitasFormatoFua', 'WebS_AdmisionCitasFormatoFua');

        Route::post('/WebS_EliminarCita', 'WebS_EliminarCita');
        Route::post('/WebS_Listas_Paciente', 'WebS_Listas_Paciente');
        Route::post('/WebS_Pacientes_CitasPendientes_BuscarFiltro', 'WebS_Pacientes_CitasPendientes_BuscarFiltro');
        Route::post('/WebS_Programacion_CuposDisponibles', 'WebS_Programacion_CuposDisponibles');


        //Hisotirales Adicionales a Citas
        Route::post('/WebS_CitasFiltrar', 'WebS_CitasFiltrar');
        Route::post('/WebS_Interconsultas_Lista_BuscarFiltro', 'WebS_Interconsultas_Lista_BuscarFiltro');
        Route::post('/WebS_Pacientes_CitaControl_Lista_BuscarFiltro', 'WebS_Pacientes_CitaControl_Lista_BuscarFiltro');


        //Agregar Cita Control
        Route::post('/WebS_PacienteConsultarCitaControl', 'WebS_PacienteConsultarCitaControl');
        Route::post('/WebS_InsertarCitaControl', 'WebS_InsertarCitaControl');


        //Agregar Cita Proxima 
        Route::post('/WebS_CitaProximaCE_BuscarFiltro', 'WebS_CitaProximaCE_BuscarFiltro');
        Route::post('/WebS_CitaProximaCE_UpdateEstado', 'WebS_CitaProximaCE_UpdateEstado'); 


        //Agregar PreQuirurgico
        Route::post('/WebS_PreQuirurgico_BuscarFiltro', 'WebS_PreQuirurgico_BuscarFiltro');
        Route::post('/WebS_InsertarInterconsulta_PreQuirurgico', 'WebS_InsertarInterconsulta_PreQuirurgico');
        Route::post('/WebS_EliminarInterconsulta_PreQuirurgico', 'WebS_EliminarInterconsulta_PreQuirurgico');


        //validaciones
        Route::post('/WebS_Validar_Cuentas_Pendientes', 'WebS_Validar_Cuentas_Pendientes');
        Route::post('/WebS_Validar_EmpleadosEspecialidades', 'WebS_Validar_EmpleadosEspecialidades');


        //Demanda Insatisfecha
        Route::post('/InsertarCitaDemandaInsatisfecha', 'InsertarCitaDemandaInsatisfecha');
        Route::post('/WebS_DemandaInsatisfecha_Lista_BuscarFiltro', 'WebS_DemandaInsatisfecha_Lista_BuscarFiltro');
    });
