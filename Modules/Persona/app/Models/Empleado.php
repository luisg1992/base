<?php

namespace Modules\Persona\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empleado extends Model
{
    protected $table = 'Empleados';
    protected $primaryKey = 'IdEmpleado';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ApellidoPaterno',
        'ApellidoMaterno',
        'Nombres',
        'IdCondicionTrabajo',
        'IdTipoEmpleado',
        'DNI',
        'CodigoPlanilla',
        'FechaIngreso',
        'FechaAlta',

        'Usuario',
        'Clave',
        'loginEstado',
        'loginPC',

        'FechaNacimiento',
        'idTipoDestacado',
        'IdEstablecimientoExterno',
        'HisCodigoDigitador',
        'ReniecAutorizado',
        'idTipoDocumento',
        'idSupervisor',
        'esActivo',
        'AccedeVWeb',
        'ClaveVWeb',

        'sexo',
        'pais',
        'idSexo',
        'idEspecialidades',
        'ImagenFirma',
        'ImagenFoto',
    ];

    protected $casts = [
        'IdEmpleado' => 'int',
        'IdCondicionTrabajo' => 'int',
        'IdTipoEmpleado' => 'int',
//        'FechaIngreso' => 'datetime',
//        'FechaAlta' => 'datetime',
//        'FechaNacimiento' => 'datetime',
        'loginEstado' => 'int',
        'idTipoDestacado' => 'int',
        'IdEstablecimientoExterno' => 'int',
        'idTipoDocumento' => 'int',
        'idSupervisor' => 'int',
        'ReniecAutorizado' => 'boolean',
        'esActivo' => 'boolean',
        'AccedeVWeb' => 'boolean',
        'sexo' => 'int',
        'pais' => 'int',
        'idSexo' => 'int',
        'idEspecialidades' => 'int',
    ];

    public function tipoCondicionTrabajo(): BelongsTo
    {
        return $this->belongsTo(TipoCondicionTrabajo::class, 'IdCondicionTrabajo', 'IdCondicionTrabajo');
    }

    public function tipoEmpleado(): BelongsTo
    {
        return $this->belongsTo(TipoEmpleado::class, 'IdTipoEmpleado', 'IdTipoEmpleado');
    }

    public function usuarioRelacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'IdEmpleado', 'IdEmpleado');
    }

    public function medico(): HasOne
    {
        return $this->hasOne(Medico::class, 'IdEmpleado', 'IdEmpleado');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(EmpleadoRol::class, 'IdEmpleado', 'IdEmpleado');
    }

    public function cargos(): HasMany
    {
        return $this->hasMany(EmpleadoCargo::class, 'idEmpleado', 'IdEmpleado');
    }

    public function especialidades(): HasMany
    {
        return $this->hasMany(EmpleadoEspecialidad::class, 'idEmpleado', 'IdEmpleado');
    }

    public function lugaresTrabajos(): HasMany
    {
        return $this->hasMany(EmpleadoLugarTrabajo::class, 'idEmpleado', 'IdEmpleado');
    }
}
