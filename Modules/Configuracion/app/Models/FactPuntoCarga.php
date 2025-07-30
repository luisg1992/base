<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class FactPuntoCarga extends Model
{
    protected $table = 'FactPuntosCarga';

    protected $primaryKey = 'IdPuntoCarga';
//    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdPuntoCarga',
        'Descripcion',
        'TipoPunto',
        'IdUPS',
        'idServicio',
    ];

    protected $casts = [
        'IdPuntoCarga' => 'int',
        'IdUPS' => 'int',
        'idServicio' => 'int'
    ];
}
