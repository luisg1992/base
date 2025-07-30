<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEmpleado extends Model
{
    protected $table = 'TiposEmpleado';
    protected $primaryKey = 'IdTipoEmpleado';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'TipoEmpleadoHIS',
        'EsProgramado',
        'TipoEmpleadoSIS',
        'EsColegiatura',
        'TipoEspecialidadSIS',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
