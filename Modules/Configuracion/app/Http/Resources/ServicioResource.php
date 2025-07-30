<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->TipoEdad === 1) {
            $minimaEdad = intdiv($this->MinimaEdad, 365);
            $maximaEdad = intdiv($this->maximaEdad, 365);
        } elseif ($this->TipoEdad === 2) {
            $minimaEdad = intdiv($this->MinimaEdad, 30);
            $maximaEdad = intdiv($this->maximaEdad, 30);
        } elseif ($this->TipoEdad === 3) {
            $minimaEdad = $this->MinimaEdad;
            $maximaEdad = $this->maximaEdad;
        } else {
            $minimaEdad = 0;
            $maximaEdad = 0;
        }

        if ($this->IdTipoServicio === 1) {
            $UsaGalenHos = $this->UsaGalenHos;
            $UsaGalenHosEmergencia = false;
        } else {
            $UsaGalenHos = false;
            $UsaGalenHosEmergencia = $this->UsaGalenHos;
        }

        return [
            'id' => $this->IdServicio,
            'IdServicio' => $this->IdServicio,
            'Nombre' => $this->Nombre,
            'IdEspecialidad' => $this->IdEspecialidad,
            'IdTipoServicio' => $this->IdTipoServicio,
            'Codigo' => $this->Codigo,
            'SVG' => $this->SVG,
            'IdProducto' => $this->IdProducto,
            'soloTipoSexo' => $this->soloTipoSexo,
            'TipoEdad' => $this->TipoEdad,
            'MinimaEdad' => $minimaEdad,
            'maximaEdad' => $maximaEdad,
            'codigoServicioSEM' => $this->codigoServicioSEM,
            'ubicacionSEM' => $this->ubicacionSEM,
            'codigoServicioHIS' => $this->codigoServicioHIS,
            'CostoCeroCE' => $this->CostoCeroCE === 'S',
            'idEstado' => $this->idEstado,
            'Triaje' => $this->Triaje,
            'EsObservacionEmergencia' => $this->EsObservacionEmergencia,
            'UsaModuloNinoSano' => $this->UsaModuloNinoSano,
            'UsaModuloMaterno' => $this->UsaModuloMaterno,
            'UsaGalenHos' => $UsaGalenHos,
            'UsaGalenHosEmergencia' => $UsaGalenHosEmergencia,
            'UsaFUA' => $this->UsaFUA,
            'codigoServicioSuSalud' => $this->codigoServicioSuSalud,
            'codigoServicioFUA' => $this->codigoServicioFUA,
            'FuaTipoAnexo2015' => $this->FuaTipoAnexo2015,
            'MaxCuposCitasAdelantadas' => $this->MaxCuposCitasAdelantadas,
            'MaxCuposAdicionales' => $this->MaxCuposAdicionales,
            'codigoServicioRenaes' => $this->codigoServicioRenaes,
            'TiempoPromProcedimiento' => $this->TiempoPromProcedimiento,
            'terapiaTipo' => $this->terapiaTipo,
            'terapiaGhoraInicio' => $this->terapiaGhoraInicio,
            'terapiaGduracion' => $this->terapiaGduracion,
            'terapiaNpacientes' => $this->terapiaNpacientes,
            'IdEspecialidadGroup' => $this->IdEspecialidadGroup,
            'CodigoPrestacionSIS' => $this->CodigoPrestacionSIS,
            'IdTipoUsoServicio' => $this->IdTipoUsoServicio,
            'CuposRefCon' => $this->CuposRefCon,
            'CodigoCE' => $this->CodigoCE,
            'TienePuntoDeCarga' => (bool)$this->factPuntoCarga,
            'IdTipoConsultorio' => $this->IdTipoConsultorio,
            'TieneEspecialidadRelacionada' => (bool)$this->TieneEspecialidadRelacionada,
        ];
    }
}
