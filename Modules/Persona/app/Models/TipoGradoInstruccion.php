<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGradoInstruccion extends Model
{
    protected $table = 'TiposGradoInstruccion';
    protected $primaryKey = 'IdGradoInstruccion';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'sip2000',
        'codigoReniec',
        'IdTipoDIRIS',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
