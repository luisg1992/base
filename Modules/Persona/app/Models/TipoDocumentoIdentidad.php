<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoIdentidad extends Model
{
    protected $table = 'TiposDocIdentidad';
    protected $primaryKey = 'IdDocIdentidad';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'Abreviatura',
        'CodigoSUNASA',
        'CodigoHIS',
        'CodigoSIS',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
