<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $table = 'Servicios';

    protected $primaryKey = 'IdServicio';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'IdTipoServicio',
        'IdEspecialidad',
        'Codigo',
        'Numero',
        'SVG',
        'IdProducto',
        'soloTipoSexo',
        'maximaEdad',
        'codigoServicioSEM',
        'ubicacionSEM',
        'codigoServicioHIS',
        'CostoCeroCE',
        'MinimaEdad',
        'idEstado',
        'Triaje',
        'EsObservacionEmergencia',
        'UsaModuloNinoSano',
        'UsaModuloMaterno',
        'UsaGalenHos',
        'TipoEdad',
        'UsaFUA',
        'codigoServicioSuSalud',
        'codigoServicioFUA',
        'FuaTipoAnexo2015',
        'MaxCuposCitasAdelantadas',
        'MaxCuposAdicionales',
        'codigoServicioRenaes',
        'TiempoPromProcedimiento',
        'terapiaTipo',
        'terapiaGhoraInicio',
        'terapiaGduracion',
        'terapiaNpacientes',
        'IdEspecialidadGroup',
        'CodigoPrestacionSIS',
        'IdTipoUsoServicio',
        'CuposRefCon',
        'CodigoCE',
        'IdTipoConsultorio',
        'TieneEspecialidadRelacionada',
    ];

    protected $casts = [
        'IdEspecialidad' => 'int',
        'IdTipoServicio' => 'int',
        'IdProducto' => 'int',
        'soloTipoSexo' => 'int',
        'maximaEdad' => 'int',
        'MinimaEdad' => 'int',
        'idEstado' => 'int',
        'Triaje' => 'int',
        'EsObservacionEmergencia' => 'int',
        'UsaModuloNinoSano' => 'int',
        'UsaModuloMaterno' => 'int',
        'UsaGalenHos' => 'int',
        'TipoEdad' => 'int',
        'UsaFUA' => 'int',
        'FuaTipoAnexo2015' => 'int',
        'MaxCuposCitasAdelantadas' => 'int',
        'MaxCuposAdicionales' => 'int',
        'TiempoPromProcedimiento' => 'int',
        'terapiaTipo' => 'int',
        'terapiaGduracion' => 'int',
        'terapiaNpacientes' => 'int',
        'IdEspecialidadGroup' => 'int',
        'IdTipoUsoServicio' => 'int',
        'CuposRefCon' => 'int',
        'codigoServicioFUA' => 'int',
        'codigoServicioSuSalud' => 'int',
        'codigoServicioHIS' => 'int',
        'IdTipoConsultorio' => 'int',
        'TieneEspecialidadRelacionada' => 'boolean'
    ];

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'IdEspecialidad', 'IdEspecialidad');
    }

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(TipoServicio::class, 'IdTipoServicio', 'IdTipoServicio');
    }

    public function tipoConsultorio(): BelongsTo
    {
        return $this->belongsTo(TipoConsultorio::class, 'IdTipoConsultorio', 'IdTipoConsultorio');
    }

    public function factPuntoCarga(): BelongsTo
    {
        return $this->belongsTo(FactPuntoCarga::class, 'IdServicio', 'idServicio');
    }

    public function serviciosAtencionesAdicionales(): HasMany
    {
        return $this->hasMany(ServicioAtencionSimultanea::class, 'IdServicio', 'idServicio');
    }
}
