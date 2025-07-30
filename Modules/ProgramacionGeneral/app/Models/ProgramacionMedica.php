<?php

namespace Modules\ProgramacionGeneral\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Configuracion\Models\Servicio;
use Modules\Configuracion\Models\TipoServicio;
use Modules\Persona\Models\Medico;

class ProgramacionMedica extends Model
{
    protected $table = 'ProgramacionMedica';

    protected $primaryKey = 'IdProgramacion';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'IdMedico' => 'int',
        'IdTurno' => 'int',
        'IdServicio' => 'int',
        'IdEspecialidad' => 'int',
    ];

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class, 'IdTurno', 'IdTurno');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'IdMedico', 'IdMedico');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'IdServicio', 'IdServicio');
    }
}
