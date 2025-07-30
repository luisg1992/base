<?php

namespace Modules\ConsultaExterna\Http\Resources;

use App\Core\Table\Cell;
use App\Core\Table\Button;
use Illuminate\Http\Request;
use App\Helpers\ModuloHelper;
use App\Helpers\ImagenBase64Helper;
use App\Core\Table\ButtonBuilder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TriajeCECollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $acciones = [
            'visualizar' => Button::botonVisualizar(),//Button::botonVisualizar()->label('VER ATENCIÓN MÉDICA'),
            'editar' => Button::botonEditar(),//Button::botonEditar()->label('MODIFICAR ATENCIÓN MÉDICA'),
            /* 'cambiar.estado' => fn($row) => Button::botonEstado($row->Estado),
            'visualizar' => Button::botonVisualizar(),
            'editar' => Button::botonEditar(), */
            'eliminar' => Button::botonEliminar(),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);

            return [
                'id' => $row->idAtencion,
                'Paciente' => Cell::composite(
                    [
                        ($row->TipoDocumento ?? '') . ': ' . ($row->NroDocumento ?? '').' - H.C: '. ($row->NroHistoriaClinica ?? ''),
                        'PACIENTE: ' . ($row->ApellidoPaterno ?? '').' '.($row->ApelidoMaterno ?? '').' '.($row->PrimerNombre ?? ''),
                    ]
                ),
                'IdCuentaAtencion' => $row->IdCuentaAtencion,
                'TriajeFecha' => $row->TriajeFecha,
                'Consultorio' => $row->Consultorio,
                'FechaCita' =>  Carbon::parse($row->FechaCita)->toDateString(),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
