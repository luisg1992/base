<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoOcupacion extends Model
{
    protected $table = 'TiposOcupacion';
    protected $primaryKey = 'IdTipoOcupacion';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'lolcli',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
