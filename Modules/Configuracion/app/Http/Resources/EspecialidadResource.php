<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EspecialidadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdEspecialidad,
            'Nombre' => $this->Nombre,
            'IdDepartamento' => $this->IdDepartamento,
            'TiempoPromedioAtencion' => $this->TiempoPromedioAtencion,
            'IdEspecialidadPrimaria' => $this->IdEspecialidadPrimaria,
            'IdEstado' => $this->IdEstado,
            'IdProductoConsulta' => $this->especialidadCE->IdProductoConsulta ?? null,
            'IdProductoInterconsulta' => $this->especialidadCE->IdProductoInterconsulta ?? null,
        ];
    }
}
