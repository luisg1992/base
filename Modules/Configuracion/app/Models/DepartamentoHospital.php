<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class DepartamentoHospital extends Model
{
    protected $table = 'DepartamentosHospital';

    protected $primaryKey = 'IdDepartamento';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdDepartamento',
        'Nombre',
        'Estado'
    ];

    protected $casts = [
        'IdDepartamento' => 'int',
        'Estado' => 'int',
    ];

    public function especialidades()
    {
        return $this->hasMany(Especialidad::class, 'IdDepartamento', 'IdDepartamento');
    }

    public function especialidadesPrimarias()
    {
        return $this->hasMany(EspecialidadPrimaria::class, 'IdDepartamento', 'IdDepartamento');
    }
      
}
