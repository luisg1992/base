<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PacienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdPaciente,
            'IdPaciente' => $this->IdPaciente,
            'ApellidoPaterno' => $this->ApellidoPaterno,
            'ApellidoMaterno' => $this->ApellidoMaterno,
            'PrimerNombre' => $this->PrimerNombre,
            'SegundoNombre' => $this->SegundoNombre,
            'TercerNombre' => $this->TercerNombre,
            'FechaNacimientoString' => Carbon::parse($this->FechaNacimiento)->toDateString(),
            'FechaNacimiento' => null,

            'NroDocumento' => $this->NroDocumento,
            'Telefono' => $this->Telefono,
            'DireccionDomicilio' => $this->DireccionDomicilio,
            'Autogenerado' => $this->Autogenerado,
            'IdTipoSexo' => (int)$this->IdTipoSexo,
            'IdProcedencia' => (int)$this->IdProcedencia,
            'IdGradoInstruccion' => (int)$this->IdGradoInstruccion,
            'IdEstadoCivil' => (int)$this->IdEstadoCivil,
            'IdDocIdentidad' => (int)$this->IdDocIdentidad,

            'IdTipoOcupacion' => (int)$this->IdTipoOcupacion,
            'IdCentroPobladoNacimiento' => $this->IdCentroPobladoNacimiento,
            'IdCentroPobladoDomicilio' => $this->IdCentroPobladoDomicilio,
            'NombrePadre' => $this->NombrePadre,
            'NombreMadre' => $this->NombreMadre,
            'NroHistoriaClinica' => $this->NroHistoriaClinica,
            'IdTipoNumeracion' => $this->IdTipoNumeracion,
            'IdCentroPobladoProcedencia' => $this->IdCentroPobladoProcedencia,
            'Observacion' => $this->Observacion,

            'IdPaisDomicilio' => (int)$this->IdPaisDomicilio,
            'IdPaisProcedencia' => (int)$this->IdPaisProcedencia,
            'IdPaisNacimiento' => (int)$this->IdPaisNacimiento,
            'IdDistritoProcedencia' => (int)$this->IdDistritoProcedencia,
            'IdDistritoDomicilio' => (int)$this->IdDistritoDomicilio,
            'IdDistritoNacimiento' => (int)$this->IdDistritoNacimiento,

            'FichaFamiliar' => $this->FichaFamiliar,
            'IdEtnia' => (int)$this->IdEtnia,
            'GrupoSanguineo' => $this->GrupoSanguineo,
            'FactorRh' => $this->FactorRh,
            'UsoWebReniec' => $this->UsoWebReniec,

            'IdIdioma' => (int)$this->IdIdioma,
            'Email' => $this->Email,
            'madreDocumento' => $this->madreDocumento,
            'madreApellidoPaterno' => $this->madreApellidoPaterno,
            'madreApellidoMaterno' => $this->madreApellidoMaterno,
            'madrePrimerNombre' => $this->madrePrimerNombre,
            'madreSegundoNombre' => $this->madreSegundoNombre,
            'NroOrdenHijo' => $this->NroOrdenHijo,

            'madreTipoDocumento' => (int)$this->madreTipoDocumento,
            'Sector' => $this->Sector,
            'Sectorista' => $this->Sectorista,
            'PacienteCrearNroAutogenerado' => $this->PacienteCrearNroAutogenerado,
            'id_etnia' => (int)$this->id_etnia,
            'IdReligion' => (int)$this->IdReligion,

//            'Anexo11' => $this->Anexo11,
//            'Anexo12' => $this->Anexo12,
//            'Archivo' => $this->Archivo,
//            'NombreArchivo11' => $this->NombreArchivo11,
//            'NombreArchivo12' => $this->NombreArchivo12,
            'IdPAcienteSIGESA' => $this->IdPAcienteSIGESA,
            'IdFlagHC' => $this->IdFlagHC,
            'ImagenFirma' => $this->ImagenFirma,
            'ImagenFoto' => $this->ImagenFoto,
        ];
    }
}
