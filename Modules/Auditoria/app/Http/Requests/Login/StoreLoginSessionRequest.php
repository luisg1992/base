<?php

namespace Modules\Auditoria\Http\Requests\Login;  

use Illuminate\Foundation\Http\FormRequest;

class StoreLoginSessionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Puedes personalizar esto según la lógica de permisos
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'hora_inicio_sesion' => 'required|date',
            'hora_cierre_sesion' => 'nullable|date',
            'ultimo_intento_at' => 'nullable|date',
            'razon' => 'nullable|string|max:255',
            'direccion_ip' => 'nullable|ip',
            'agente_usuario' => 'nullable|string',
        ];
    }
}
