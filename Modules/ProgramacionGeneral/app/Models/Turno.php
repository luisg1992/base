<?php

namespace Modules\ProgramacionGeneral\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Configuracion\Models\TipoServicio;

class Turno extends Model
{
    protected $table = 'Turnos';

    protected $primaryKey = 'IdTurno';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Codigo',
        'Descripcion',
        'HoraInicio',
        'HoraFin',
        'IdTipoServicio',
        'IdEspecialidad',
        'IdTipoTurnoRef',
        'Estado',
    ];

    protected $casts = [
        'Estado' => 'int',
        'IdTipoServicio' => 'int', 
    ];

    public function tipoTurno()
    {
        return $this->belongsTo(TipoTurno::class, 'IdTipoTurnoRef', 'IdTurno');
    }

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'IdTipoServicio', 'IdTipoServicio');
    }
}
