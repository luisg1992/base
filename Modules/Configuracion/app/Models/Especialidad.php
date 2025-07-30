<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'Especialidades';

    protected $primaryKey = 'IdEspecialidad';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'IdDepartamento',
        'TiempoPromedioAtencion',
        'IdEspecialidadPrimaria',
        'IdEstado'
    ];

    protected $casts = [
        'IdEspecialidadPrimaria' => 'int',
        'IdDepartamento' => 'int',
        'IdEstado' => 'int',
        'Numero' => 'int',
    ];

    public function departamento()
    {
        return $this->belongsTo(DepartamentoHospital::class, 'IdDepartamento', 'IdDepartamento');
    }

    public function especialidadPrimaria()
    {
        return $this->belongsTo(EspecialidadPrimaria::class, 'IdEspecialidadPrimaria', 'IdEspecialidadPrimaria');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'IdEspecialidad', 'IdEspecialidad');
    }

    public function especialidadCE()
    {
        return $this->hasOne(EspecialidadCE::class, 'IdEspecialidad', 'IdEspecialidad');
    }
}
