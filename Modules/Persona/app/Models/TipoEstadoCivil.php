<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEstadoCivil extends Model
{
    protected $table = 'TiposEstadoCivil';
    protected $primaryKey = 'IdEstadoCivil';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'lolcli',
        'sip2000',
        'IdReniec'
    ];

    protected $casts = [
        'IdEstadoCivil' => 'int',
        'IdReniec' => 'int',
    ];
}
