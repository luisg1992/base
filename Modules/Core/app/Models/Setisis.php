<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Setisis extends Model
{
    protected $table = 'Setisis';

    protected $primaryKey = 'IdSetisis';
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Fecha',
        'Numero',
        'PaqueteNumero',
        'Datos',
        'Estado',
        'FechaCreacion',
        'UsuarioCreacion',
        'FechaModificacion',
        'UsuarioModificacion',
    ];

    protected $casts = [
        'IdSetisis' => 'int',
        'Fecha' => 'date',
        'Numero' => 'int',
        'Datos' => 'array',
        'Estado' => 'int',
        'FechaCreacion' => 'date',
        'FechaModificacion' => 'date',
        'UsuarioCreacion' => 'int',
        'UsuarioModificacion' => 'int',
    ];
}
