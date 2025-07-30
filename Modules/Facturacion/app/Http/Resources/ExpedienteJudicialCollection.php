<?php

namespace Modules\Facturacion\Http\Resources;

use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpedienteJudicialCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
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
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdProgramaExpedienteJudicial,
                'IdFuenteFinanciamiento' => $row->IdFuenteFinanciamiento,
                'IdTipoFinanciamiento' => $row->IdTipoFinanciamiento,
                'IdPaciente' => $row->IdPaciente,
                'IdProgramaInstitucion' => $row->IdProgramaInstitucion,
                'IdTipoDocumento' => $row->IdTipoDocumento,
                'IdTipoServicio' => $row->IdTipoServicio,
                'IdEspecialidad' => $row->IdEspecialidad,
                'NumeroDocumento' => $row->NumeroDocumento ?? '-',
                'FechaDocumento' => $row->FechaDocumento ?? '-',
                'FechaVencimiento' => $row->FechaVencimiento ?? '-',
                'NumeroExpedienteTramiteDocumentario' => $row->NumeroExpedienteTramiteDocumentario ?? '-',
                'FechaRegistro' => $row->FechaRegistro,
                'IdEmpleadoRegistra' => $row->IdEmpleadoRegistra,
                'Estado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons(),

                'Paciente' => (optional($row->paciente)->PrimerNombre ?? '-').' '.(optional($row->paciente)->ApellidoPaterno ?? '').' '.(optional($row->paciente)->ApellidoMaterno ?? ''),
                'TipoFinancimiento' => optional($row->tipoFinancimiento)->Descripcion ?? '-',
                'FuenteFinanciamiento' => optional($row->fuenteFinanciamiento)->Descripcion ?? '-',
                'NroDocumento' => optional($row->paciente)->NroDocumento ?? '-',
                'Servicio' => optional($row->tipoServicio)->Descripcion ?? '-',
                'Especialidad' => optional($row->especialidad)->Nombre ?? '-',
                'ProgramaInstitucion' => optional($row->programaInstitucion)->Descripcion ?? '-',
                'TipoDocumento' => optional($row->tipoDocumento)->Descripcion ?? '-',
            ];
        });
    }
}
