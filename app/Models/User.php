<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Persona\Models\Empleado;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles {
        hasPermissionTo as protected hasRolePermissionTo;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'estado',
        'IdEmpleado',
        'TerminalLogin',
        'IpTerminalLogin',
        'MacLogin',
        'Celular',
        'CorreoElectronico',
        'UltimoInicioSesion',
        'UltimaActividad',
        'EstaEnLinea',
    ];

    public $timestamps = false;

    protected $casts = [
        'estado' => 'int',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function esAdministrador()
    {
        return $this->hasRole('ADMINISTRADOR DE SISTEMA');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'IdEmpleado', 'IdEmpleado');
    }

    public function deniedPermissions()
    {
        return $this->belongsToMany(Permission::class, 'denied_permissions');
    }

    public function getEffectivePermissions()
    {
        // Permisos de roles + permisos directos
        $allPermissions = $this->getAllPermissions();
        // Lista de exclusiones
        $deniedIds = $this->deniedPermissions()->pluck('permissions.id')->toArray();

        // Solo elimina los que estén explícitamente denegados
        return $allPermissions->filter(function ($permission) use ($deniedIds) {
            return !in_array($permission->id, $deniedIds);
        });
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $perm = Permission::findByName($permission, $guardName);
        // ¿Está denegado?
        if ($this->deniedPermissions()->where('permissions.id', $perm->id)->exists()) {
            return false;
        }
        // Usa el método original de Spatie para heredar del rol y directos
        return $this->hasRolePermissionTo($permission, $guardName);
    }
}
