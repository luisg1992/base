<?php


namespace Modules\Configuracion\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoMotivoTriajeEmergenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdMotivo,
            'Descripcion' => $this->Descripcion,
            'IdPrioridad' => $this->IdPrioridad,
            'IdServicio' => $this->IdServicio,
            'IdEstado' => $this->IdEstado
        ];
    }
}