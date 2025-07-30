<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGravedadAtencion extends Model
{
    protected $table = 'TiposGravedadAtencion';

    protected $primaryKey = 'IdTipoGravedad';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'IdTipoGravedad',
        'Descripcion',
        'OrdenPrioridad',
        'Estado'
    ];

    protected $casts = [
        'IdTipoGravedad' => 'int',
        'OrdenPrioridad' => 'int',
        'Estado' => 'int'
    ];
}
