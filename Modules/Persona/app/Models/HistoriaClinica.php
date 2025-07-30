<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    protected $table = 'HistoriasClinicas';
    protected $primaryKey = 'NroHistoriaClinica';
    public $timestamps = false;

    protected $fillable = [
        'NroHistoriaClinica',
        'FechaCreacion',
        'FechaPasoAPasivo',
        'IdTipoHistoria',
        'IdEstadoHistoria',
        'IdPaciente',
        'IdTipoNumeracion',
        'NroHistoriaClinicaAnterior',
        'IdTipoNumeracionAnterior',
        'HistoriaSistemaAnterior',
        'Observacion',
        'Caja',
        'Lote',
        'ResponsableDepurado',
        'UltimoServicio'
    ];

    protected $casts = [
        'IdTipoHistoria' => 'int',
        'IdEstadoHistoria' => 'int',
        'IdPaciente' => 'int',
        'IdTipoNumeracion' => 'int',
        'NroHistoriaClinicaAnterior' => 'int',
        'IdTipoNumeracionAnterior' => 'int',
    ];
}
