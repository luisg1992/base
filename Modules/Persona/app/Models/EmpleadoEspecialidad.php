<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Configuracion\Models\Especialidad;

class EmpleadoEspecialidad extends Model
{
    protected $table = 'EmpleadosEspecialidades';

    public $timestamps = false;

    protected $fillable = [
        'IdEmpleado',
        'IdEspecialidad',
    ];

    protected $casts = [
        'IdEmpleado' => 'int',
        'IdEspecialidad' => 'int',
    ];

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'IdEspecialidad', 'IdEspecialidad');
    }
}
