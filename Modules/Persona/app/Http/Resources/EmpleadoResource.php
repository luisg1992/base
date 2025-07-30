<?php

namespace Modules\Persona\Http\Resources;

use App\Http\Controllers\AppController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tipoLugaresLaborales = collect(cache_configuracion_tipo_lugares_laborales());
        $medico = $this->medico;
        $usuarioRelacion = $this->usuarioRelacion;
        $Colegiatura = null;
        $LoteHIS = null;
        $rne = null;
        $idColegioHIS = '00';
        $egresado = false;
        $especialidades = [];
        if ($medico) {
            $Colegiatura = $medico->Colegiatura;
            $LoteHIS = $medico->LoteHIS;
            $rne = $medico->rne;
            $idColegioHIS = $medico->idColegioHIS;
            $egresado = $medico->egresado;
            $especialidades = $medico->especialidades->transform(function ($row) {
                return [
                    'IdEspecialidad' => $row->IdEspecialidad,
                    'idEstado' => $row->idEstado,
                    'Nombre' => $row->especialidad->Nombre,
                ];
            });
        }

        $usuario_roles = $usuarioRelacion? $usuarioRelacion->roles->transform(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }) : [];

        $tieneUsuario = false;
        if($this->usuarioRelacion) {
            $tieneUsuario = true;
        }

        $tieneWebEspecialidades = false;
        if(count($this->especialidades) > 0) {
            $tieneWebEspecialidades = true;
        }

        $web_especialidades = $this->especialidades? $this->especialidades->transform(function ($row) {
            return [
                'id' => $row->IdEspecialidad,
                'name' => $row->especialidad->Nombre,
            ];
        }) : [];

        return [
            'id' => $this->IdEmpleado,
            'IdEmpleado' => $this->IdEmpleado,
            'ApellidoPaterno' => $this->ApellidoPaterno,
            'ApellidoMaterno' => $this->ApellidoMaterno,
            'Nombres' => $this->Nombres,
            'IdCondicionTrabajo' => $this->IdCondicionTrabajo,
            'IdTipoEmpleado' => $this->IdTipoEmpleado,
            'DNI' => trim($this->DNI),
            'CodigoPlanilla' => $this->CodigoPlanilla,
            'FechaIngreso' => $this->FechaIngreso,
            'FechaAlta' => $this->FechaAlta,

            'UsuarioWeb' => optional($this->usuarioRelacion)->email,
            'loginEstado' => $this->loginEstado,
            'loginPC' => $this->loginPC,

            'FechaNacimientoString' => Carbon::parse($this->FechaNacimiento)->toDateString(),
            'FechaNacimiento' => null,
            'idTipoDestacado' => $this->idTipoDestacado,
            'IdEstablecimientoExterno' => $this->IdEstablecimientoExterno,
            'HisCodigoDigitador' => $this->HisCodigoDigitador,
            'ReniecAutorizado' => $this->ReniecAutorizado,
            'idTipoDocumento' => $this->idTipoDocumento,
            'idSupervisor' => $this->idSupervisor,
            'esActivo' => $this->esActivo,
            'AccedeVWeb' => $this->AccedeVWeb,
            'ClaveVWeb' => $this->ClaveVWeb,
            'ImagenFirma' => $this->ImagenFirma,
            'ImagenFoto' => $this->ImagenFoto,

            'sexo' => $this->sexo,
            'pais' => $this->pais,
            'idSexo' => $this->idSexo,
            'idEspecialidades' => $this->idEspecialidades,
            'roles' => $this->roles->transform(function ($row) {
                return [
                    'IdRol' => $row->IdRol,
                    'Nombre' => $row->rol->Nombre,
                ];
            }),
            'cargos' => $this->cargos->transform(function ($row) {
                return [
                    'IdCargo' => $row->idCargo,
                    'Nombre' => $row->tipoCargo->Cargo,
                ];
            }),
            'lugaresTrabajos' => $this->lugaresTrabajos->transform(function ($row) use ($tipoLugaresLaborales) {
                $idLaboraArea = $row->idLaboraArea;
                $idLaboraSubArea = $row->idLaboraSubArea;
                $area = $tipoLugaresLaborales->firstWhere('value', $idLaboraArea);
                $request = new Request(['tipo' => $idLaboraArea]);
                $subAreas = collect((new AppController())->filtrarLugaresLaboralesPorTipo($request));
                $subArea = $subAreas->firstWhere('value', $idLaboraSubArea);

                return [
                    'IdLaboraArea' => $idLaboraArea,
                    'Area' => $area['label'],
                    'IdLaboraSubArea' => $idLaboraSubArea,
                    'SubArea' => $subArea['label'],
                ];
            }),
            'esProfesionalSalud' => $medico ? true : false,
            'tieneUsuario' => $tieneUsuario,
            'tieneWebEspecialidades' => $tieneWebEspecialidades,
            'Colegiatura' => trim($Colegiatura),
            'LoteHIS' => $LoteHIS,
            'rne' => $rne,
            'idColegioHIS' => $idColegioHIS,
            'egresado' => $egresado,
            'web_especialidades' => $web_especialidades,
            'usuario_roles' => $usuario_roles,

            'Usuario' => $this->Usuario,
            'ClaveActual' => $this->Clave,
        ];
    }
}
