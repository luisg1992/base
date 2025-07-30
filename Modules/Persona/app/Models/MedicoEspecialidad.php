<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Configuracion\Models\Especialidad;

class MedicoEspecialidad extends Model
{
    protected $table = 'MedicosEspecialidad';
    protected $primaryKey = 'IdMedicoEspecialidad';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdEspecialidad',
        'IdMedico',
        'idEstado',
    ];

    protected $casts = [
        'IdMedicoEspecialidad' => 'int',
        'IdEspecialidad' => 'int',
        'IdMedico' => 'int',
        'idEstado' => 'boolean',
    ];

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'IdEspecialidad', 'IdEspecialidad');
    }
}
