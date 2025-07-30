<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Medico extends Model
{
    protected $table = 'Medicos';
    protected $primaryKey = 'IdMedico';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Colegiatura',
        'IdEmpleado',
        'LoteHIS',
        'idColegioHIS',
        'rne',
        'egresado',
    ];

    protected $casts = [
        'IdMedico' => 'int',
        'IdEmpleado' => 'int',
        'egresado' => 'boolean',
    ];

    public function empleado(): HasOne
    {
        return $this->hasOne(Empleado::class, 'IdEmpleado', 'IdEmpleado');
    }

    public function especialidades(): HasMany
    {
        return $this->hasMany(MedicoEspecialidad::class, 'IdMedico', 'IdMedico');
    }
}
