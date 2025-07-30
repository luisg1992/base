<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Persona\Models\Paciente;

class FacturacionCuentasAtencion extends Model
{
    protected $table = 'FacturacionCuentasAtencion';

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'IdCuentaAtencion';
    protected $keyType = 'int';

    protected $fillable = [
        'IdPaciente',
        'FechaApertura',
        'HoraApertura',
        'FechaCierre',
        'HoraCierre',
        'TotalExonerado',
        'TotalAsegurado',
        'TotalPagado',
        'IdEstado',
        'TotalPorPagar',
        'IdUsuarioCrea',
        'IdUsuarioModifica',
        'FechaCreacion',
        'FechaModificacion',
        'FechaRegistro',
        'IdCuentaSigesa'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'IdPaciente', 'IdPaciente');
    }
}
