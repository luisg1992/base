<?php

namespace Modules\Farmacia\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotaSalidaAlmacenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fechRegistro = Carbon::createFromFormat('Y-m-d H:i:s.u', $this->fechaCreacion);

        return [
            'id' => $this->MovNumero,
            'fechaRegistro' => $fechRegistro->format('d/m/Y'),
            'horaRegistro' => $fechRegistro->format('H:i:s'),
            'MovNumero' => $this->MovNumero,
            'Observaciones' => $this->Observaciones,
            'DocumentoIdtipo' => (int)$this->DocumentoIdtipo,
            'DocumentoNumero' => $this->DocumentoNumero,
            'idAlmacenDestino' => (int)$this->idAlmacenDestino,
            'idAlmacenOrigen' => (int)$this->idAlmacenOrigen,
            'idEstadoMovimiento' => (int)$this->idEstadoMovimiento,
            'idTipoConcepto' => (int)$this->idTipoConcepto,
            'productos' => $this->detalle,
            'datos' => (array)$this,
        ];
    }
}
