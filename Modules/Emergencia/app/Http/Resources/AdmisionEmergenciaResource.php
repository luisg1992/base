<?php

namespace Modules\Emergencia\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AdmisionEmergenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        dd($this);

//        WebS_AdmisionEmergenciaHospitalizacion_Consultar_Diagnostico(
//            @IdCuentaAtencion Int

        $diagnosticos = DB::select('EXEC WebS_AdmisionEmergenciaHospitalizacion_Consultar_Diagnostico @IdAtencion = ?',
            [$this->IdAtencion]
        );

        return [
            'IdMedicoIngreso' => (int) $this->IdMedicoIngreso,
            'IdTipoGravedad' => (int) $this->IdTipoGravedad,
            'IdTipoFinanciamiento' => (int) $this->IdTipoFinanciamiento,
            'IdFuenteFinanciamiento' => (int) $this->IdFuenteFinanciamiento,
            'IdOrigenAtencion' => (int) $this->IdOrigenAtencion,
            'IdServicioIngreso' => (int) $this->IdServicioIngreso,
            'NombreAcompaniante' => $this->NombreAcompaniante,
            'TelefonoAcompaniante' => $this->TelefonoAcompaniante,
            'FechaIngreso' => $this->FechaIngreso,
            'HoraIngreso' => $this->HoraIngreso,
            'RecienNacido' => (int) $this->RecienNacido,
            'IdTipoDocumento' => (int) $this->IdTipoDocumento,
            'NroDocumento' => $this->NroDocumento,
            'diagnosticos' => collect($diagnosticos)->transform(function ($row) {
                return [
                    'Descripcion' => $row->Diagnostico,
                ];
            }),
            'IdCausaExternaMorbilidad' => $this->IdCausaExternaMorbilidad,
            'IdLugarEvento' =>  $this->IdLugarEvento,
            'IdTipoEvento' =>  $this->IdTipoEvento,
            'IdSeguridad' =>  $this->IdSeguridad,
            'IdRelacionAgresorVictima' =>  $this->IdRelacionAgresorVictima,
            'IdClaseAccidente' =>  $this->IdClaseAccidente,
            'IdTipoVehiculo' => $this->IdTipoVehiculo,
            'IdTipoTransporte' =>  $this->IdTipoTransporte,
            'IdUbicacionLesionado' =>  $this->IdUbicacionLesionado,
            'IdPosicionLesionadoALAB' =>  $this->IdPosicionLesionadoALAB,
            'IdGrupoOcupacionalALAB' =>  $this->IdGrupoOcupacionalALAB,
            'IdTipoAgenteAGAN' => $this->IdTipoAgenteAGAN,
            'IdTriajeEmergencia' => (int) $this->IdTriajeEmergencia,
            'IdPaciente' => (int) $this->IdPaciente,

        ];
    }
}
