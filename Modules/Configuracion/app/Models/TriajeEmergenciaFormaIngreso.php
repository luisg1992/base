<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TriajeEmergenciaFormaIngreso extends Model
{
    protected $table = 'TriajeEmergenciaFormasIngresos';

    protected $primaryKey = 'IdFormaIngreso';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'int'
    ];

    public function estadosIngresos()
    {
        return $this->hasMany(TriajeEmergenciaEstadoIngreso::class, 'IdFormaIngreso', 'IdFormaIngreso');
    }
}
