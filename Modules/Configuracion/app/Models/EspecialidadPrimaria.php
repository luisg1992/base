<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class EspecialidadPrimaria extends Model
{
    protected $table = 'EspecialidadesPrimarias';

    protected $primaryKey = 'IdEspecialidadPrimaria';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Estado',
        'IdDepartamento'
    ];

    protected $casts = [
        'Estado' => 'int',
        'IdDepartamento' => 'int',
        'IdEspecialidad' => 'int',
    ];

    public function departamento()
    {
        return $this->belongsTo(DepartamentoHospital::class, 'IdDepartamento', 'IdDepartamento');
    }

    public function especialidades()
    {
        return $this->hasMany(Especialidad::class, 'IdEspecialidadPrimaria', 'IdEspecialidad');
    } 

    public function ubicacionesFisicas()
    {
        return $this->hasMany(UbicacionFisica::class, 'IdEspecialidadPrimaria', 'IdEspecialidadPrimaria');
    }
}
