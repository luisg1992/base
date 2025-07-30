<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCargo extends Model
{
    protected $table = 'TiposCargo';
    protected $primaryKey = 'idTipoCargo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Cargo',
        'Estado',
    ];

    protected $casts = [
        'Estado' => 'int',
    ];
}
