<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    protected $table = 'Atenciones';
    protected $primaryKey = 'IdAtencion';
    public $timestamps = false;

    protected $fillable = [
        'IdPaciente',
        'Edad',
        'FechaIngreso',
        'HoraIngreso',
        'IdDestinoAtencion',
        'IdTipoCondicionAlServicio',
        'IdTipoCondicionAlEstab',
        'IdServicioIngreso',
        'IdMedicoIngreso',
        'IdEspecialidadMedico',
        'IdMedicoEgreso',
        'FechaEgreso',
        'HoraEgreso',
        'IdOrigenAtencion',
        'FechaEgresoAdministrativo',
        'HoraEgresoAdministrativo',
        'IdCondicionAlta',
        'IdTipoAlta',
        'IdServicioEgreso',
        'IdCamaIngreso',
        'IdCamaEgreso',
        'IdTipoGravedad',
        'IdTipoEdad',
        'IdCuentaAtencion',
        'IdTipoServicio',
        'IdFormaPago',
        'idFuenteFinanciamiento',
        'idEstadoAtencion',
        'EsPacienteExterno',
        'idSunasaPacienteHistorico',
        'IdTiposCondicionAsegurado',
        'EstablecimientoSalud',
        'IdMedicoRecibeHospitalizacion',
        'IdPasoHospitalizacion',
        'IdServicioEstabaEmergencia',
        'IdTipoServicioPasoHosp',
        'NroContrareferencia',
        'IdLlegadaPaciente',
        'PdfAtencion',
        'NombreArchivoPdf',
        'NombreArchivoConsentimientoInformado',
        'IdTriajeEmergencia'
    ];

    protected $casts = [
        'IdAtencion' => 'integer',
        'IdPaciente' => 'integer',
        'Edad' => 'integer',
        'FechaIngreso' => 'datetime',
        'IdDestinoAtencion' => 'integer',
        'IdTipoCondicionAlServicio' => 'integer',
        'IdTipoCondicionAlEstab' => 'integer',
        'IdServicioIngreso' => 'integer',
        'IdMedicoIngreso' => 'integer',
        'IdEspecialidadMedico' => 'integer',
        'IdMedicoEgreso' => 'integer',
        'FechaEgreso' => 'datetime',
        'IdOrigenAtencion' => 'integer',
        'FechaEgresoAdministrativo' => 'datetime',
        'IdCondicionAlta' => 'integer',
        'IdTipoAlta' => 'integer',
        'IdServicioEgreso' => 'integer',
        'IdCamaIngreso' => 'integer',
        'IdCamaEgreso' => 'integer',
        'IdTipoGravedad' => 'integer',
        'IdTipoEdad' => 'integer',
        'IdCuentaAtencion' => 'integer',
        'IdTipoServicio' => 'integer',
        'IdFormaPago' => 'integer',
        'idFuenteFinanciamiento' => 'integer',
        'idEstadoAtencion' => 'integer',
        'EsPacienteExterno' => 'boolean',
        'idSunasaPacienteHistorico' => 'integer',
        'IdTiposCondicionAsegurado' => 'integer',
        'IdMedicoRecibeHospitalizacion' => 'integer',
        'IdPasoHospitalizacion' => 'integer',
        'IdServicioEstabaEmergencia' => 'integer',
        'IdTipoServicioPasoHosp' => 'integer',
        'IdLlegadaPaciente' => 'integer',
        'IdTriajeEmergencia' => 'integer',
    ];
}
