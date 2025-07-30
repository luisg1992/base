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

class ServicioCollection extends ResourceCollection
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
            'duplicar' => Button::make()->label('Duplicar')->action('duplicar')->icon('ti ti-copy')
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            if ($row->IdTipoServicio === 1) {
                $acciones['ups'] = Button::make()->action('ups')->label('UPS adicionales');
            }

            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdServicio,
                'IdServicio' => $row->IdServicio,
                'Servicio' => strtoupper($row->Nombre),
                'IdEspecialidad' => $row->IdEspecialidad,
                'Departamento' => strtoupper(optional(optional($row->especialidad)->departamento)->Nombre),
                'EspecialidadPrimaria' => strtoupper(optional(optional($row->especialidad)->especialidadPrimaria)->Nombre),
                'Especialidad' => strtoupper(optional($row->especialidad)->Nombre),
                'IdTipoServicio' => $row->IdTipoServicio,
                'TipoServicio' => strtoupper(optional($row->tipoServicio)->Descripcion),
                'Codigo' => $row->Codigo,
                'Nombre' => $row->Nombre,
                'SVG' => $row->SVG,
                'IdProducto' => $row->IdProducto,
                'soloTipoSexo' => $row->soloTipoSexo,
                'maximaEdad' => $row->maximaEdad,
                'codigoServicioSEM' => $row->codigoServicioSEM,
                'ubicacionSEM' => $row->ubicacionSEM,
                'codigoServicioHIS' => $row->codigoServicioHIS,
                'CostoCeroCE' => $row->CostoCeroCE,
                'MinimaEdad' => $row->MinimaEdad,
                'Triaje' => $row->Triaje,
                'EsObservacionEmergencia' => $row->EsObservacionEmergencia,
                'UsaModuloNinoSano' => $row->UsaModuloNinoSano,
                'UsaModuloMaterno' => $row->UsaModuloMaterno,
                'UsaGalenHos' => $row->UsaGalenHos,
                'TipoEdad' => $row->TipoEdad,
                'UsaFUA' => $row->UsaFUA,
                'codigoServicioSuSalud' => $row->codigoServicioSuSalud,
                'codigoServicioFUA' => $row->codigoServicioFUA,
                'FuaTipoAnexo2015' => $row->FuaTipoAnexo2015,
                'MaxCuposCitasAdelantadas' => $row->MaxCuposCitasAdelantadas,
                'MaxCuposAdicionales' => $row->MaxCuposAdicionales,
                'codigoServicioRenaes' => $row->codigoServicioRenaes,
                'TiempoPromProcedimiento' => $row->TiempoPromProcedimiento,
                'terapiaTipo' => $row->terapiaTipo,
                'terapiaGhoraInicio' => $row->terapiaGhoraInicio,
                'terapiaGduracion' => $row->terapiaGduracion,
                'terapiaNpacientes' => $row->terapiaNpacientes,
                'IdEspecialidadGroup' => $row->IdEspecialidadGroup,
                'CodigoPrestacionSIS' => $row->CodigoPrestacionSIS,
                'IdTipoUsoServicio' => $row->IdTipoUsoServicio,
                'CuposRefCon' => $row->CuposRefCon,
                'CodigoCE' => $row->CodigoCE,
                'idEstado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
