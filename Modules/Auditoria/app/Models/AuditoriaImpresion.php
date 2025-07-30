<?php

namespace Modules\Auditoria\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaImpresion extends Model
{
    protected $table = 'AuditoriaImpresion';
    protected $connection = 'sqlsrvAuditoria';

    protected $primaryKey = 'IdAuditoriaImpresion';

    public $timestamps = false; // Deshabilitar timestamps si no tienes `created_at` y `updated_at`

    protected $fillable = [
        'IdExterna',
        'DataJson',
        'FechaRegistro',
        'IdEmpleadoRegistra',
        'Ipv4',
        'Ipv6',
        'Hostname',
        'Mac',
    ];

    protected $casts = [
        'FechaRegistro' => 'datetime', // Asegurar que la fecha se maneje correctamente
        'DataJson' => 'array', // Si usas JSON, Laravel lo convierte automáticamente en un array
    ];
}
