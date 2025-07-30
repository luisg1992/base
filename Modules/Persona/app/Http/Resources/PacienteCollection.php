<?php

namespace Modules\Persona\Http\Resources;

use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ImagenBase64Helper;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PacienteCollection extends ResourceCollection
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
            'actualizar.datos.reniec' => Button::make()
                ->label('Actualizar datos RENIEC')
                ->action("actualiza_datos_reniec")
                ->icon('ti ti-mood-edit'),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $EstadoBadge = $row->esActivo
                ? Badge::make('ACTIVO', 'success')
                : Badge::make('INACTIVO', 'danger');

            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);

            $avatar = $row->IdTipoSexo ?'sexo'.$row->IdTipoSexo.'.gif':'avatar.png';
            $default = 'assets/img/' . $avatar;
            $imagePath = ImagenBase64Helper::obtenerUrlImagen(
                $row->ImagenFoto,
                'paciente/fotos',
                $default);

            return [
                'id' => $row->IdPaciente,
                'IdPaciente' => $row->IdPaciente,
                'ImagenFoto' => Cell::avatar( $imagePath),
                'NombreCompleto' => $row->ApellidoPaterno . ' ' . $row->ApellidoMaterno . ', ' . $row->PrimerNombre . ' ' . $row->SegundoNombre . ' ' . $row->TercerNombre,
                'FechaNacimiento' => $row->FechaNacimiento->format('d/m/Y'),
                'NroHistoriaClinica' => $row->NroHistoriaClinica,
                'NroDocumento' => $row->NroDocumento,
//                'Estado' => $EstadoBadge,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
