<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TriajeEmergenciaEstadoIngreso extends Model
{
    protected $table = 'TriajeEmergenciaEstadosIngresos';

    protected $primaryKey = 'IdEstadoIngreso';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdFormaIngreso',
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'IdFormaIngreso' => 'int',
        'Estado' => 'int'
    ];

    public function formaIngreso()
    {
        return $this->belongsTo(TriajeEmergenciaFormaIngreso::class, 'IdFormaIngreso', 'IdFormaIngreso');
    }
}
