<?php

namespace Modules\Configuracion\Http\Resources;

use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UbicacionFisicaCollection extends ResourceCollection
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
            'visualizar' => Button::botonVisualizar(),
            'editar' => Button::botonEditar(),
            'eliminar' => Button::botonEliminar(),
            'cambiar.estado' => fn($row) => Button::botonEstado($row->Estado),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            // Badge para Departamento
            $Departamento = optional($row->especialidadPrimaria) &&
            optional(optional($row->especialidadPrimaria)->departamento)->Nombre
                ? strtoupper($row->especialidadPrimaria->departamento->Nombre)
                : 'NO ASIGNADO';

            // Badge para EspecialidadPrimaria
            $EspecialidadPrimaria = optional($row->especialidadPrimaria) &&
                optional($row->especialidadPrimaria)->Nombre
                ? strtoupper($row->especialidadPrimaria->Nombre)
                : 'NO ASIGNADO';

            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdUbicacionFisica,
                'Nombre' => $row->Nombre,
                'TipoUbicacionFisica' => $row->TipoUbicacionFisica,
                'IdEspecialidad' => $row->IdEspecialidad,
                'Departamento' => $Departamento,
                'EspecialidadPrimaria' => $EspecialidadPrimaria,
                'Estado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
