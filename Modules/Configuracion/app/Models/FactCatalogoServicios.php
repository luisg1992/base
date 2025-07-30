<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class FactCatalogoServicios extends Model
{
    protected $table = 'FactCatalogoServicios';

    protected $primaryKey = 'IdProducto';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Codigo',
        'Nombre',
        'IdServicioGrupo',
        'IdServicioSubGrupo',
        'IdServicioSeccion',
        'IdServicioSubSeccion',
        'IdPartida',
        'IdCentroCosto',
        'CodMINSA',
        'CodMINSAnoActualiza',
        'EsCPT',
        'idOpcs',
        'NombreMINSA',
        'codigoSIS',
        'idEstado',
        'idGrupo',
        'Duracion',
        'LabResultadoAutomatico',
        'codigoCPMS',
        'CodigoSISCPT',
        'IndSIS',
    ];

    protected $casts = [
        'IdProducto'              => 'int',
        'IdServicioGrupo'         => 'int',
        'IdServicioSubGrupo'      => 'int',
        'IdServicioSeccion'       => 'int',
        'IdServicioSubSeccion'    => 'int',
        'IdPartida'               => 'int',
        'IdCentroCosto'           => 'int',
        'CodMINSAnoActualiza'     => 'boolean',
        'EsCPT'                   => 'int',
        'idOpcs'                  => 'int',
        'idEstado'                => 'int',
        'idGrupo'                 => 'int',
        'Duracion'                => 'int',
        'LabResultadoAutomatico'  => 'int',
        'IndSIS'                  => 'boolean',
    ];
}
