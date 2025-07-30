<?php

namespace Modules\Emergencia\Http\Resources;

use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Emergencia\Models\Atencion;

class TriajeEmergenciaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param Request $request
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        return $this->collection->transform(function ($row, $key) use ($valorPermission) {
            $fechaAdmision = '';
            if ($row->atencion) {
                $fechaAdmision = Carbon::parse($row->atencion->FechaIngreso)->format('d/m/Y') . ' - ' . $row->atencion->HoraIngreso;
                $acciones = [
                    'visualizar' => Button::botonVisualizar(),
                ];
            } else {
                $acciones = [
                    'visualizar' => Button::botonVisualizar(),
                    'editar' => Button::botonEditar(),
                    'eliminar' => Button::botonEliminar(),
                    'cambiar.estado' => Button::botonEstado($row->Estado),
                ];
            }
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);

            $paciente = Cell::multiLine([
                (optional($row->tipoDocTriaje)->Abreviatura ?? '-') . ' ' . $row->NroDocTriaje,
                $row->PrimerNombreTriaje . ' ' . $row->ApellidoPaternoTriaje . ' ' . $row->ApellidoMaternoTriaje
            ]);

            if ($row->IdTipoGravedad === 1) {
                $gravedad = Cell::badgeText($row->tipoGravedad->Descripcion, 'danger');
            } else if ($row->IdTipoGravedad === 2) {
                $gravedad = Cell::badgeText($row->tipoGravedad->Descripcion, 'contrast');
            } else if ($row->IdTipoGravedad === 3) {
                $gravedad = Cell::badgeText($row->tipoGravedad->Descripcion, 'warn');
            } else if ($row->IdTipoGravedad === 4) {
                $gravedad = Cell::badgeText($row->tipoGravedad->Descripcion, 'success');
            } else {
                $gravedad = '';
            }

            return [
                'id' => $row->IdTriajeEmergencia,
                'CodigoTriaje' => $row->CodigoTriaje,
                'Paciente' => $paciente,
                'FechaHora' => Carbon::parse($row->TriajeFecha)->format('d/m/Y - H:i'),
                'FechaHoraAdmision' => $fechaAdmision,
                'Edad' => $row->TriajeAnios . 'a ' . $row->TriajeMeses . 'm ' . $row->TriajeDias . 'd',
                'Prioridad' => $gravedad,
                'Servicio' => optional($row->servicio)->Nombre ?? '-',
                'MotivoIngreso' => optional($row->servicio)->Descripcion ?? '-',
                'FormaIngreso' => optional($row->formaIngreso)->Descripcion ?? '-',
                'EstadoIngreso' => optional($row->estadoIngreso)->Descripcion ?? '-',
                'MedicoTriaje' => $row->IdMedicoTriaje,
                'Estado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
