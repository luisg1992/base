<?php

namespace Modules\ConsultaExterna\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEstadoInterconsulta extends Model
{
    protected $table = 'TiposEstadosInterconsulta';

    protected $primaryKey = 'IdEstadoInterconsulta';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'idestado'
    ];

    protected $casts = [
        'idestado' => 'int'
    ];
}
