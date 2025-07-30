<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;

class AreaTramitaSeguros extends Model
{
    protected $table = 'AreaTramitaSeguros';
    protected $primaryKey = 'idAreaTramitaSeguros';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'int',



    ];
}