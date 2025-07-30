<?php

namespace Modules\Persona\Http\Controllers;

use App\Helpers\ImagenBase64Helper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use Modules\Api\Http\Controllers\Reniec\ReniecAvanzadoController;
use Modules\Persona\DataTables\PacienteDataTable;
use Modules\Persona\Http\Requests\PacienteRequest;
use Modules\Persona\Http\Resources\PacienteResource;
use Modules\Persona\Models\HistoriaClinica;
use Modules\Persona\Models\Paciente;

class PacienteController extends Controller
{
    use PacienteDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Persona/Paciente/PacienteIndex');
    }

    public function show($id)
    {
        $record = Paciente::query()
            ->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new PacienteResource($record);
    }

    public function store(PacienteRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            if ($data['BuscadoPorReniec']) {
                if (!is_null($data['ImagenFoto64']) && $data['ImagenFoto64'] !== '') {
                    $res = ImagenBase64Helper::almacenarImagenBase64($data['ImagenFoto64'], 'paciente/fotos');
                    if ($res['success']) {
                        $data['ImagenFoto'] = $res['uuid'];
                    }
                }

                if (!is_null($data['ImagenFirma64']) && $data['ImagenFirma64'] !== '') {
                    $res = ImagenBase64Helper::almacenarImagenBase64($data['ImagenFirma64'], 'paciente/firmas');
                    if ($res['success']) {
                        $data['ImagenFirma'] = $res['uuid'];
                    }
                }
            }

            if (!empty($request->id)) {
                $record = Paciente::query()->findOrFail($request->id);
                $record->update($data);
            } else {
                $data['NroHistoriaClinica'] = $data['NroDocumento'];
                $record = Paciente::query()->create($data);
                HistoriaClinica::query()->create([
                    'IdTipoNumeracion' => $record->IdTipoNumeracion,
                    'NroHistoriaClinica' => $record->NroHistoriaClinica,
                    'FechaCreacion' => now(),
                    'IdTipoHistoria' => 1,
                    'IdEstadoHistoria' => 1,
                    'IdPaciente' => $record->IdPaciente,
                ]);
            }

            DB::commit();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA');
        } catch (\Throwable $th) {
            DB::rollBack();
            return obtener_respuesta_error($th->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new PacienteResource(Paciente::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->Nombre . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }

    public function changeActive(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = Paciente::query()->find($request->input('id'));
            $record->update([
                'esActivo' => !$record->esActivo,
            ]);
            cache_configuracion_especialidades_limpiar();
            $nombreCompleto = $record->ApellidoPaterno . ' ' . $record->ApellidoMaterno . ' ' . $record->Nombres;
            return obtener_respuesta_exito('EL ESTADO DE ' . $nombreCompleto . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->esActivo) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new PacienteResource(Paciente::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->nombre . '?',
            true,
            $record
        );
    }

    public function destroy(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }
        DB::beginTransaction();
        try {
            $record = Paciente::query()->findOrFail($request->input('id'));
            HistoriaClinica::query()->where('IdPaciente', $record->IdPaciente)->delete();
            $record->delete();
            cache_core_parametros_limpiar();
            DB::commit();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function actualizarPacientePorReniec(Request $request)
    {
        $record = Paciente::query()->find($request->input('id'));

        $request->merge([
            'dni' => $record->NroDocumento,
        ]);
        $res = (new ReniecAvanzadoController())->consultar($request);

        if ($res['success']) {
            $_data = $res['data'];

            $data['ApellidoPaterno'] = $_data['ApellidoPaterno'];
            $data['ApellidoMaterno'] = $_data['ApellidoMaterno'];

            $nombres = explode(' ', $_data['Nombres']);
            $data['PrimerNombre'] = $nombres[0] ?? null;
            $data['SegundoNombre'] = $nombres[1] ?? null;
            $data['TercerNombre'] = $nombres[2] ?? null;

            $data['IdTipoSexo'] = (int)$_data['Sexo'];

            $estadoCivil = collect(cache_persona_tipo_estados_civil())->firstWhere('IdReniec', (int)$_data['EstadoCivil']);
            if ($estadoCivil) {
                $data['IdEstadoCivil'] = $estadoCivil['id'];
            }

            $data['DireccionDomicilio'] = $_data['Direccion'];

            $ubigeoDomicilioCodigo = (int)($_data['UbigeoDepartamentoDomicilio'] + $_data['UbigeoProvinciaDomicilio'] + $_data['UbigeoDistritoDomicilio']);
            $ubigeoDomicilio = collect(cache_configuracion_ubigeos())->firstWhere('IdReniec', $ubigeoDomicilioCodigo);
            if ($ubigeoDomicilio) {
                $data['IdDistritoDomicilio'] = $ubigeoDomicilio['id'];
            }

            $ubigeoNacimientoCodigo = (int)($_data['UbigeoDepartamentoNacimiento'] + $_data['UbigeoProvinciaNacimiento'] + $_data['UbigeoDistritoNacimiento']);
            $ubigeoNacimiento = collect(cache_configuracion_ubigeos())->firstWhere('IdReniec', $ubigeoNacimientoCodigo);
            if ($ubigeoNacimiento) {
                $data['IdDistritoNacimiento'] = $ubigeoNacimiento['id'];
            }

            $fechaNacimiento = explode('/', $_data['FechaNacimiento']);
            $data['FechaNacimiento'] = $fechaNacimiento[2] . '-' . $fechaNacimiento[1] . '-' . $fechaNacimiento[0];

            if (!is_null($_data['ImagenFoto']) && $_data['ImagenFoto'] !== '') {
                $res = ImagenBase64Helper::almacenarImagenBase64($_data['ImagenFoto'], 'paciente/fotos');
                if ($res['success']) {
                    $data['ImagenFoto'] = $res['uuid'];
                }
            }

            if (!is_null($_data['ImagenFirma']) && $_data['ImagenFirma'] !== '') {
                $res = ImagenBase64Helper::almacenarImagenBase64($_data['ImagenFirma'], 'paciente/firmas');
                if ($res['success']) {
                    $data['ImagenFirma'] = $res['uuid'];
                }
            }

            $record->update($data);

            return obtener_respuesta_exito('El paciente fue actualizado de forma exitosa.');
        }

        return $res;
    }

    public function WebS_Pacientes_BuscarFiltro(Request $request)
    {
        $request->validate([
            'filtro' => 'required|string|min:8'
        ]);

        try {
            $filtro = $request->input('filtro');
            $resultados = DB::select('EXEC WebS_Pacientes_BuscarFiltro ?', [$filtro]);

            return response()->json([
                'success' => true,
                'data' => $resultados
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar pacientes.',
                'error' => $e->getMessage()
            ]);
        }
    }
}
