<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Seguridad\Models\Role;

class EmpleadoRol extends Model
{
    protected $table = 'UsuariosRoles';

    public $timestamps = false;

    protected $fillable = [
        'IdRol',
        'IdEmpleado',
    ];

    protected $casts = [
        'IdRol' => 'int',
        'IdEmpleado' => 'int',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'IdRol', 'IdRol');
    }
}
