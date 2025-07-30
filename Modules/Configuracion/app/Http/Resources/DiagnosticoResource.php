<?php


namespace Modules\Configuracion\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiagnosticoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdDiagnostico,
            'CodigoCIE10' => $this->CodigoCIE10,
            'Descripcion' => $this->Descripcion,
            'IdCapitulo' => $this->IdCapitulo,
            'IdGrupo' => $this->IdGrupo,
            'IdCategoria' => $this->IdCategoria,
            'CodigoExportacion' => $this->CodigoExportacion,
            'CodigoCIE9' => $this->CodigoCIE9,
            'CodigoCIE10' => $this->CodigoCIE10,
            'Gestacion' => $this->Gestacion,
            'Morbilidad' => $this->Morbilidad,
            'Intrahospitalario' => $this->Intrahospitalario,
            'Restriccion' => $this->Restriccion,
            'EdadMaxDias' => $this->EdadMaxDias,
            'EdadMinDias' => $this->EdadMinDias,
            'IdTipoSexo' => $this->IdTipoSexo,
            'ClaseDxHIS' => $this->ClaseDxHIS,
            'DescripcionMINSA' => $this->DescripcionMINSA,
            'codigoCIEsinPto' => $this->codigoCIEsinPto,
            'FechaInicioVigencia' => $this->FechaInicioVigencia,
            'EsActivo' => $this->EsActivo
        ];
    }
}
