<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDestinoAtencion extends Model
{
    protected $table = 'TiposDestinoAtencion';

    protected $primaryKey = 'IdDestinoAtencion';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdDestinoAtencion',
        'Codigo',
        'Descripcion',
        'IdTipoServicio',
        'TipoServicioHosp',
        'DestinoSEM',
        'id_destinoAseguradoSIS'
    ];

    protected $casts = [
        'IdDestinoAtencion' => 'int',
        'IdTipoServicio' => 'int',
        'TipoServicioHosp' => 'int'
    ];
}
