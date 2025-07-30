<?php

namespace Modules\Auditoria\Http\Resources\Login;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginSessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id, 
            'usuario' => [
                'id' => $this->user->id,
                'nombre' => $this->user->name,
                'correo' => $this->user->email,
            ],
            
            // Tiempos de sesión
            'hora_inicio_sesion' => $this->hora_inicio_sesion,
            'hora_cierre_sesion' => $this->hora_cierre_sesion,
            'ultimo_intento_at' => $this->ultimo_intento_at,
            
            // Información del fallo (si aplica)
            'razon' => $this->razon,
            'direccion_ip' => $this->direccion_ip,
            'agente_usuario' => $this->agente_usuario,
            
            // Timestamps del registro
            'creado_en' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'actualizado_en' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
