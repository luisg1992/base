<?php

namespace Modules\ConsultaExterna\Http\Resources;

use App\Core\Table\Cell;
use App\Core\Table\Button;
use Illuminate\Http\Request;
use App\Helpers\ModuloHelper;
use App\Helpers\ImagenBase64Helper;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AtencionMedicaCECollection extends ResourceCollection
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
            'visualizar' => Button::botonVisualizar()->label('VER ATENCIÓN MÉDICA'),
            'editar' => Button::botonEditar()->label('MODIFICAR ATENCIÓN MÉDICA'),
            'cambiar.estado' => fn($row) => Button::botonEstado($row->Estado),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);
            $avatar = $row->IdTipoSexo ? 'sexo' . $row->IdTipoSexo . '.gif' : 'avatar.png';
            $default = 'assets/img/' . $avatar;
            $imagePath = ImagenBase64Helper::obtenerUrlImagen(
                $row->ImagenFoto,
                'paciente/fotos',
                $default
            );
            // ✅ Badge para el sexo del paciente
            // Si es de tipo 1 (por ejemplo, masculino), se muestra en color verde (success), si no, en azul (info)
            $SexoDescripcion = Cell::badgeText(
                $row->Sexo,
                $row->IdTipoSexo == 1 ? 'success' : 'info'
            );

            // ✅ Badge para la fuente de financiamiento
            // Se evalúa el ID como entero para aplicar un color representativo
            $idFuente = intval($row->IdFuenteFinanciamiento);
            $colorFuente = match ($idFuente) {
                1       => 'warn', // Fuente 1: SIS
                3       => 'success', // Fuente 3: Particular
                default => 'info',    // Otros casos
            };

            $FuenteFinanciamiento = Cell::badgeText(
                'F.FINANCIAMIENTO: ' . ($row->FuentesFinanciamiento ?? 'NO ASIGNADO'),
                $colorFuente
            );

            // ✅ Badge para el estado de pago de la cita
            $estadoPagoRaw = $row->PagoCita ?? 'SIN ESTADO';
            $estadoPago = trim(str_replace('Estado: ', '', $estadoPagoRaw));

            $colorPago = match ($estadoPago) {
                'Abierto',
                'Pendiente pago Seguros',
                'ReembolsoParcial',
                'No llega al Servicio Hosp'          => 'warn',

                'Pagado',
                'Con Alta médica',
                'Con Alta Adm,deuda y  Garante'      => 'success',

                'Cerrado',
                'Anulado',
                'Cerrado automático'                 => 'danger',

                default                              => 'info',
            };

            $PagoCita = Cell::badgeText('ESTADO: ' . $estadoPago, $colorPago);





            // ✅ Badge para el estado de atención en la cola de citas
            // Se aplica un color dependiendo del estado textual
            $estado = $row->EstadosColaCitas;
            $color = match ($estado) {
                'Citado'        => 'info',
                'Llego'         => 'info',
                'Llamando'      => 'primary',
                'En Atención'   => 'warn',
                'Atendido'      => 'success',
                'NSP/Ausente'   => 'danger',
                default         => 'dark', // Por si se agrega un nuevo estado no contemplado
            };
            $EstadoBadge = Cell::badgeText('ESTADO: ' . $estado, $color);

            // ✅ Badge para el estado de firma
            // Si el valor es exactamente 'SIN FIRMA', se muestra rojo (danger)
            // Si contiene cualquier otro valor (una fecha/hora), se considera firmado (verde)
            $colorFirmado = trim($row->Firmado) === 'SIN FIRMA' ? 'danger' : 'success';
            $Firmado = Cell::badgeText(('FIRMADO: ' . $row->Firmado), $colorFirmado);

            return [
                'id' => $row->IdCuentaAtencion,
                'OrigenCita' => Cell::composite([
                    'CUENTA: ' . ($row->IdCuentaAtencion ?? 'NO ASIGNADO'),
                    'ORIGEN: ' . ($row->OrigenCita ?? 'GALENOS'),
                    [($PagoCita)],
                    [($FuenteFinanciamiento)],
                ]),
                'HoraAtencionInicio' => Cell::composite([
                    'H.INICIO: ' . ($row->HoraAtencionInicio ?? ''),
                    'H.FIN: ' . ($row->HoraAtencionInicio ?? ''),
                    [($EstadoBadge)],
                ]),
                'ImagenFoto' => Cell::avatar($imagePath),
                'Paciente' => Cell::composite(
                    [
                        ($row->Doc_Identidad) . ' - H.C: ' . ($row->HistoriaClinica ?? ''),
                        'PACIENTE: ' . ($row->Paciente ?? ''),
                        'CONTACTO: ' . ($row->Telefono ?? ''),
                        [($SexoDescripcion)],
                    ]
                ),
                'Servicio' => Cell::multiLine([
                    'CITA: ' . (($row->FechaAtencion ?? '') . ' - ' . ($row->HoraInicio ?? '')),
                    'SERVICIO: ' . ($row->Servicio ?? ''),
                    'MÉDICO: ' . ($row->Medico ?? ''),
                ]),
                'Firmado' => Cell::composite(
                    [
                        [$Firmado]
                    ]
                ),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
