<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpleadoCargo extends Model
{
    protected $table = 'EmpleadosCargos';

    public $timestamps = false;

    protected $fillable = [
        'idEmpleado',
        'idCargo',
    ];

    protected $casts = [
        'idEmpleado' => 'int',
        'idCargo' => 'int',
    ];

    public function tipoCargo(): BelongsTo
    {
        return $this->belongsTo(TipoCargo::class, 'idCargo', 'idTipoCargo');
    }
}
