<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpleadoLugarTrabajo extends Model
{
    protected $table = 'EmpleadosLugarDeTrabajo';

    public $timestamps = false;

    protected $fillable = [
        'idEmpleado',
        'idLaboraArea',
        'idLaboraSubArea',
    ];

    protected $casts = [
        'idEmpleado' => 'int',
        'idLaboraArea' => 'int',
        'idLaboraSubArea' => 'int',
    ];
}
