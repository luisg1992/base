<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Configuracion\Models\Servicio;
use Modules\Configuracion\Models\TipoGravedadAtencion;
use Modules\Configuracion\Models\TipoMotivoTriajeEmergencia;
use Modules\Configuracion\Models\TriajeEmergenciaEstadoIngreso;
use Modules\Configuracion\Models\TriajeEmergenciaFormaIngreso;
use Modules\Persona\Models\TipoDocumentoIdentidad;

class TriajeEmergencia extends Model
{
    protected $table = 'TriajeEmergencia';
    protected $primaryKey = 'IdTriajeEmergencia';
    public $timestamps = false;

    protected $fillable = [
        'CodigoTriaje',
        'IdEmpleado',
        'IdPaciente',
        'IdFuenteFinanciamiento',
        'IdTipoFinanciamiento',
        'IdServicio',
        'IdTipoGravedad',
        'TriajeFecha',
        'TriajeHora',
        'TriajeAnios',
        'TriajeMeses',
        'TriajeDias',
        'TriajeEscalaDolor',
        'TriajeSinRespiratorio',
        'TriajePresion',
        'TriajeFrecCardiaca',
        'TriajeFrecRespiratoria',
        'TriajeSaturacionOxigeno',
        'TriajeTemperatura',
        'TriajeTalla',
        'TriajePeso',
        'TriajeIMC',
        'TriajeObservacion',
        'Estacion',
        'IdMedicoTriaje',
        'IdMedicoTopico',
        'Acomp',
        'Diag_1',
        'Diag_2',
        'Diag_3',
        'EstadoTriaje',
        'idCuentaAtencion',
        'IdTipoDocTriaje',
        'NroDocTriaje',
        'ApellidoPaternoTriaje',
        'ApellidoMaternoTriaje',
        'PrimerNombreTriaje',
        'FechaNacimientoTriaje',
        'IdMotivoIngreso',
        'EstablecimientoSalud',
        'IdTipoTriajeDiferenciado',
        'Estado',
        'IdFormaIngreso',
        'IdEstadoIngreso',
        'IdAtencion',
        'HoraInicio',
        'HoraTermino'
    ];

    protected $casts = [
        'IdTriajeEmergencia' => 'integer',
        'IdEmpleado' => 'integer',
        'IdFuenteFinanciamiento' => 'integer',
        'IdTipoFinanciamiento' => 'integer',
        'IdServicio' => 'integer',
        'IdTipoGravedad' => 'integer',
        'IdTipoDocTriaje' => 'integer',
        'IdMotivoIngreso' => 'integer',
        'IdFormaIngreso' => 'integer',
        'IdEstadoIngreso' => 'integer',


        'TriajeFrecCardiaca' => 'integer',
        'TriajeFrecRespiratoria' => 'integer',
        'IdMedicoTriaje' => 'integer',
        'IdMedicoTopico' => 'integer',
        'Diag_1' => 'integer',
        'Diag_2' => 'integer',
        'Diag_3' => 'integer',
        'idCuentaAtencion' => 'integer',
        'IdPaciente' => 'integer',
        'Estado' => 'integer',
        'TriajeAnios' => 'integer',
        'TriajeMeses' => 'integer',
        'TriajeDias' => 'integer',
        'IdAtencion' => 'integer',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'IdServicio');
    }

    public function tipoGravedad()
    {
        return $this->belongsTo(TipoGravedadAtencion::class, 'IdTipoGravedad');
    }

    public function tipoDocTriaje()
    {
        return $this->belongsTo(TipoDocumentoIdentidad::class, 'IdTipoDocTriaje', 'IdDocIdentidad');
    }

    public function motivoIngreso()
    {
        return $this->belongsTo(TipoMotivoTriajeEmergencia::class, 'IdMotivoIngreso', 'IdMotivo');
    }

    public function formaIngreso()
    {
        return $this->belongsTo(TriajeEmergenciaFormaIngreso::class, 'IdFormaIngreso');
    }

    public function estadoIngreso()
    {
        return $this->belongsTo(TriajeEmergenciaEstadoIngreso::class, 'IdEstadoIngreso');
    }

    public function atencion() :BelongsTo
    {
        return $this->belongsTo(Atencion::class, 'IdAtencion', 'IdAtencion');
    }
}
