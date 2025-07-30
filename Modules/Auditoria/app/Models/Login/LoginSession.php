<?php

namespace Modules\Auditoria\Models\Login;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginSession extends Model
{
    use HasFactory;

    protected $table = 'sesiones_login';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'hora_inicio_sesion',
        'hora_cierre_sesion',
        'ultimo_intento_at',
        'razon',
        'direccion_ip',
        'agente_usuario',
    ];

    protected $casts = [
        'hora_inicio_sesion' => 'datetime',
        'hora_cierre_sesion' => 'datetime',
        'ultimo_intento_at' => 'datetime',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
