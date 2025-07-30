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

class EmpleadoCollection extends ResourceCollection
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
            'actualizar.datos.reniec' => Button::make()
                ->label('Actualizar datos RENIEC')
                ->action("actualiza_datos_reniec")
                ->icon('ti ti-mood-edit'),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            if ($row->usuarioRelacion) {
                $acciones['eliminar.usuario'] = Button::make()
                    ->label('Eliminar usuario')
                    ->action('eliminarUsuario')
                    ->icon('ti ti-user-x');
            } else {
                $acciones['generar.usuario'] = Button::make()
                    ->label('Generar usuario')
                    ->action('generarUsuario')
                    ->icon('ti ti-user-plus');
            }

            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);

            $tieneUsuario = $row->usuarioRelacion ? Badge::make('SI', 'success') : Badge::make('NO', 'danger');

            $avatar = $row->idSexo ?'sexo'.$row->idSexo.'.gif':($row->sexo ?'sexo'.$row->sexo.'.gif':'avatar.png');
            $default = 'assets/img/' . $avatar;
            $imagePath = ImagenBase64Helper::obtenerUrlImagen(
                $row->ImagenFoto,
                'empleado/fotos',
                $default);

            return [
                'id' => $row->IdEmpleado,
                'IdEmpleado' => $row->IdEmpleado,
                'ImagenFoto' => Cell::avatar( $imagePath),
                'NombreCompleto' => $row->ApellidoPaterno . ' ' . $row->ApellidoMaterno . ', ' . $row->Nombres,
                'DNI' => $row->DNI,
                'TipoEmpleado' => $row->tipoEmpleado->Descripcion,
                'TipoCondicionTrabajo' => $row->tipoCondicionTrabajo->Descripcion,
                'Estado' => Cell::badgeEstado($row),
                'Usuario' => $tieneUsuario,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
