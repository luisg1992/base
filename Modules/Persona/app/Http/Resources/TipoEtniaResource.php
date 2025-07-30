<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoEtniaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->codetni,
            'desetni' => $this->desetni,
            'codgen' => $this->codgen,
            'etnias' => $this->etnias,
            'id_etnia' => $this->id_etnia,
            'Estado' => $this->Estado
        ];
    }
}
