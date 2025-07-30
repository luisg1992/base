<?php

namespace Modules\ProgramacionGeneral\Models;


use Illuminate\Database\Eloquent\Model;

class TipoTurno extends Model
{
    protected $table = 'RefConTurnosUPS';

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'IdTurno';
    protected $keyType = 'int';

    protected $fillable = [
        'codTurno',
        'Turno',
    ];

}
