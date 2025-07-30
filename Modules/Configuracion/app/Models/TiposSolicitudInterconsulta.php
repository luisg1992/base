<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TiposSolicitudInterconsulta extends Model
{
    protected $table = 'TiposSolicitudInterconsulta';

    protected $primaryKey = 'IdSolicitudInter';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdSolicitudInter',
        'Descripcion',
        'IdEstado'
    ];

    protected $casts = [
        'IdSolicitudInter' => 'int',
        'IdEstado' => 'int'
    ];
}
