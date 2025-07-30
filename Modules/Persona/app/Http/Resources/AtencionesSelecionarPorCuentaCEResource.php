<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtencionesSelecionarPorCuentaCEResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->IdCuentaAtencion,
            'ApellidoMaterno' => $this->ApellidoMaterno,
            'ApellidoPaterno' => $this->ApellidoPaterno,
            'Edad' => $this->Edad,
            'EsPacienteExterno' => $this->EsPacienteExterno,
            'FechaEgreso' => $this->FechaEgreso,
            'FechaEgresoAdministrativo' => $this->FechaEgresoAdministrativo,
            'FechaIngreso' => $this->FechaIngreso,
            'FechaNacimiento' => $this->FechaNacimiento,
            'HoraEgreso' => $this->HoraEgreso,
            'HoraIngreso' => $this->HoraIngreso,
            'IdAtencion' => $this->IdAtencion,
            'IdCamaEgreso' => (int) $this->IdCamaEgreso,
            'IdDestinoAtencion' => (int) $this->IdDestinoAtencion,
            'IdDocIdentidad' => (int) $this->IdDocIdentidad,
            'IdEmpleado' => (int) $this->IdEmpleado,
            'IdEspecialidad' => (int) $this->IdEspecialidad,
            'IdEstablecimientoDestino' => (int) $this->IdEstablecimientoDestino,
            'IdEstablecimientoNoMinsaDestino' => (int) $this->IdEstablecimientoNoMinsaDestino,
            'IdEstablecimientoNoMinsaOrigen' => (int) $this->IdEstablecimientoNoMinsaOrigen,
            'IdEstablecimientoOrigen' => (int) $this->IdEstablecimientoOrigen,
            'IdEstado' => (int) $this->IdEstado,
            'IdEtnia' => (int) $this->IdEtnia,
            'IdFormaPago' => (int) $this->IdFormaPago,
            'IdMedicoEgreso' => (int) $this->IdMedicoEgreso,
            'IdMedicoIngreso' => (int) $this->IdMedicoIngreso,
            'IdPaciente' => (int) $this->IdPaciente,
            'IdServicioEgreso' => (int) $this->IdServicioEgreso,
            'IdServicioIngreso' => (int) $this->IdServicioIngreso,
            'IdTipoEdad' => (int) $this->IdTipoEdad,
            'IdTipoNumeracion' => (int) $this->IdTipoNumeracion,
            'IdTipoReferenciaDestino' => (int) $this->IdTipoReferenciaDestino,
            'IdTipoReferenciaOrigen' => (int) $this->IdTipoReferenciaOrigen,
            'IdTipoServicio' => (int) $this->IdTipoServicio,
            'IdTipoSexo' => (int) $this->IdTipoSexo,
            'NombreArchivoConsentimientoInformado' => $this->NombreArchivoConsentimientoInformado,
            'NroDocumento' => $this->NroDocumento,
            'NroHistoriaClinica' => $this->NroHistoriaClinica,
            'NroReferenciaDestino' => $this->NroReferenciaDestino,
            'NroReferenciaOrigen' => $this->NroReferenciaOrigen,
            'PrimerNombre' => $this->PrimerNombre,
            'SegundoNombre' => $this->SegundoNombre,
            'ServicioProcedencia' => $this->ServicioProcedencia,
            'Sexo' => $this->Sexo,
            'dFuenteFinanciamiento' => $this->dFuenteFinanciamiento,
            'dTipoFinanciamiento' => $this->dTipoFinanciamiento,
            'dTipoServicio' => $this->dTipoServicio,
            'estadoCta' => $this->estadoCta,
            'idAreaTramitaSeguros' => (int) $this->idAreaTramitaSeguros,
            'idEstadoAtencion' => (int) $this->idEstadoAtencion,
            'idFuenteFinanciamiento' => (int) $this->idFuenteFinanciamiento,
            'tedad' => $this->tedad,
            'tipoVenta' => $this->tipoVenta,
        ];
    }
}
