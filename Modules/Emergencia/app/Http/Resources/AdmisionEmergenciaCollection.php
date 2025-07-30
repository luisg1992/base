<?php

namespace Modules\Emergencia\Http\Resources;

use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ImagenBase64Helper;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AdmisionEmergenciaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param Request $request
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $acciones = [
            'visualizar' => Button::botonVisualizar()->label('Visualizar'),
            'anular' => Button::make()->label('Anular'),
//            'cambiar.estado' => fn($row) => Button::botonEstado($row->Estado),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);

            if ($row->IdTipoGravedad == 1) {
                $gravedad = Cell::badgeText($row->TipoGravedadDescripcion, 'danger');
            } else if ($row->IdTipoGravedad == 2) {
                $gravedad = Cell::badgeText($row->TipoGravedadDescripcion, 'contrast');
            } else if ($row->IdTipoGravedad == 3) {
                $gravedad = Cell::badgeText($row->TipoGravedadDescripcion, 'warn');
            } else if ($row->IdTipoGravedad == 4) {
                $gravedad = Cell::badgeText($row->TipoGravedadDescripcion, 'success');
            } else {
                $gravedad = '';
            }

            return [
                'id' => (int) $row->IdAtencion,
                'NroCuenta' => $row->NroCuenta,
                'Paciente' => Cell::multiLine([$row->Documento . ' - H.C.:' . $row->HistoriaClinica, $row->Paciente]),
                'FechaIngreso' => Cell::multiLine([$row->FechaIngreso, $row->HoraIngreso]),
                'FechaEgreso' => Cell::multiLine([$row->FechaEgreso, $row->HoraEgreso]),
                'Prioridad' => $gravedad,
                'FechaEgresoAdministrativo' => Cell::multiLine([$row->FechaEgresoAdministrativo, $row->HoraEgresoAdministrativo]),
                'FuenteFinanciamiento' => $row->FuenteFinanciamiento,
                'IdTipoGravedad' => (int) $row->IdTipoGravedad,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
