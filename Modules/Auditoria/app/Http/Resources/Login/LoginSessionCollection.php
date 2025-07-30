<?php

namespace Modules\Auditoria\Http\Resources\Login;

use App\Core\Table\Button;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LoginSessionCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function ($session) {
            $builder = new ButtonBuilder();
            $builder->agregarBoton(Button::botonVisualizar());

            return [
                'id' => $session->id,
                'usuario' => $session->user->name,
                'correo' => $session->user->email,
                'hora_inicio_sesion' => optional($session->hora_inicio_sesion)->format('Y/m/d H:i'),
                'hora_cierre_sesion' => optional($session->hora_cierre_sesion)->format('Y/m/d H:i'),
                'ultimo_intento_at' => optional($session->ultimo_intento_at)->format('Y/m/d H:i'),
                'razon' => $session->razon,
                'direccion_ip' => $session->direccion_ip,
                'agente_usuario' => $session->agente_usuario,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
