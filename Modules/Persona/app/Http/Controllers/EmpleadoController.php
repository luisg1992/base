<?php

namespace Modules\Persona\Http\Controllers;

use App\Helpers\ImagenBase64Helper;
use App\Models\Configuracion\DataTables\ConfiguracionDataTable;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use Modules\Api\Http\Controllers\Colegio\DoctorController;
use Modules\Api\Http\Controllers\Colegio\EnfermeraController;
use Modules\Api\Http\Controllers\Colegio\ObstetraController;
use Modules\Api\Http\Controllers\Colegio\OdontologoController;
use Modules\Api\Http\Controllers\Colegio\PsicologoController;
use Modules\Api\Http\Controllers\Reniec\ReniecAvanzadoController;
use Modules\Persona\DataTables\EmpleadoDataTable;
use Modules\Persona\Http\Requests\EmpleadoRequest;
use Modules\Persona\Http\Resources\EmpleadoResource;
use Modules\Persona\Models\Empleado;

class EmpleadoController extends Controller
{
    use EmpleadoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Persona/Empleado/EmpleadoIndex');
    }

    public function show($id)
    {
        $record = Empleado::query()
            ->with('cargos', 'cargos.tipoCargo', 'lugaresTrabajos', 'roles', 'usuarioRelacion',
                'medico', 'medico.especialidades', 'medico.especialidades.especialidad')
            ->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new EmpleadoResource($record);
    }

    public function store(EmpleadoRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['FechaNacimiento'] = $data['FechaNacimientoString'] . 'T00:00:00';

            $empleado = $this->guardarEmpleado($data);

            if (!empty($data['Usuario'])) {
                $this->guardarUsuario($empleado, $data);
            } else {
                $empleado->usuarioRelacion()->delete();
            }

            if($data['BuscadoPorReniec']) {
                if (!is_null($data['ImagenFoto64']) && $data['ImagenFoto64'] !== '') {
                    $res = ImagenBase64Helper::almacenarImagenBase64($data['ImagenFoto64'], 'empleado/fotos');
                    if ($res['success']) {
                        $empleado->update(['ImagenFoto' => $res['uuid']]);
                    }
                }

                if (!is_null($data['ImagenFirma64']) && $data['ImagenFirma64'] !== '') {
                    $res = ImagenBase64Helper::almacenarImagenBase64($data['ImagenFirma64'], 'empleado/firmas');
                    if ($res['success']) {
                        $empleado->update(['ImagenFirma' => $res['uuid']]);
                    }
                }
            }

            $this->guardarRelacionesSimples($empleado, $data);

            if (!empty($data['esProfesionalSalud'])) {
                $this->guardarDatosMedico($empleado, $data);
            } else {
                $empleado->medico?->especialidades()->delete();
                $empleado->medico?->delete();
            }

            $empleado->especialidades()->delete();
            if ($data['tieneWebEspecialidades']) {
                foreach ($data['web_especialidades'] as $row) {
                    $empleado->especialidades()->create([
                        'IdEspecialidad' => $row['id'],
                    ]);
                }
            }

            DB::commit();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $empleado);
        } catch (\Throwable $th) {
            DB::rollBack();
            return obtener_respuesta_error($th->getMessage());
        }
    }

    private function guardarEmpleado(array $data): Empleado
    {
        if (!empty($data['id'])) {
            $empleado = Empleado::with('usuarioRelacion')->findOrFail($data['id']);
            $empleado->update(collect($data)->except('Clave')->toArray());

            $empleado->roles()->delete();
            $empleado->cargos()->delete();
            $empleado->lugaresTrabajos()->delete();
        } else {
            $empleado = Empleado::create($data);
        }

        return $empleado;
    }

    private function guardarUsuario(Empleado $empleado, array $data): void
    {
        $clave = !empty($data['Clave']) ? Hash::make($data['Clave']) : null;

        if ($empleado->usuarioRelacion) {
            $empleado->usuarioRelacion->update([
                'email' => $data['Usuario'],
                'password' => $clave ?? $empleado->usuarioRelacion->password,
            ]);
        } else {
            $empleado->usuarioRelacion()->create([
                'email' => $data['Usuario'],
                'name' => $empleado->ApellidoPaterno . ' ' . $empleado->ApellidoMaterno . ', ' . $empleado->Nombres,
                'password' => $clave ?? Hash::make($data['DNI']),
                'estado' => true,
            ]);
        }

        if (!empty($data['usuario_roles'])) {
            $empleado->usuarioRelacion->syncRoles($data['usuario_roles']);
            cache_persona_empleados_usuario_roles_limpiar();
        }
    }

    private function guardarRelacionesSimples(Empleado $empleado, array $data): void
    {
        foreach ($data['roles'] ?? [] as $row) {
            $empleado->roles()->create(['IdRol' => $row['IdRol']]);
        }

        foreach ($data['cargos'] ?? [] as $row) {
            $empleado->cargos()->create(['idCargo' => $row['IdCargo']]);
        }

        foreach ($data['lugaresTrabajos'] ?? [] as $row) {
            $empleado->lugaresTrabajos()->create([
                'idLaboraArea' => $row['IdLaboraArea'],
                'idLaboraSubArea' => $row['IdLaboraSubArea'],
            ]);
        }
    }

    private function guardarDatosMedico(Empleado $empleado, array $data): void
    {
        $medico = $empleado->medico()->updateOrCreate(
            ['IdEmpleado' => $empleado->IdEmpleado],
            collect($data)->only([
                'Colegiatura', 'LoteHIS', 'idColegioHIS', 'rne', 'egresado'
            ])->toArray()
        );

        if (!empty($data['especialidades'])) {
            $medico->especialidades()->delete();
            foreach ($data['especialidades'] as $row) {
                $medico->especialidades()->create([
                    'IdEspecialidad' => $row['IdEspecialidad'],
                    'idEstado' => $row['idEstado'],
                ]);
            }
        }
    }


//    public function store(EmpleadoRequest $request)
//    {
//        DB::beginTransaction();
//        try {
//            $data = $request->all();
//
//            $data['FechaNacimiento'] = $data['FechaNacimientoString'] . 'T00:00:00';
//            if (!empty($request->id)) {
//                $record = Empleado::query()->with('usuarioRelacion')->findOrFail($request->id);
//                $record->update(collect($data)->except('Clave')->toArray());
//                $record->roles()->delete();
//                $record->cargos()->delete();
//                $record->lugaresTrabajos()->delete();
//
//                $tieneUsuario = $data['tieneUsuario'];
//                if ($tieneUsuario) {
//                    $claveNueva = $data['Clave'];
//                    $clave = '';
//                    if ($claveNueva !== '' && $claveNueva === null) {
//                        $clave = Hash::make($claveNueva);
//                    }
//
//                    if ($record->usuarioRelacion) {
//                        $record->usuarioRelacion->update([
//                            'email' => $data['Usuario']
//                        ]);
//                        if($clave !== '') {
//                            $record->usuarioRelacion->update([
//                                'password' => $clave,
//                            ]);
//                        }
//                        if ($data['usuario_roles']) {
//                            $record->usuarioRelacion->syncRoles($data['usuario_roles']);
//                        }
//                    } else {
//                        $usuarioRelacion = User::query()
//                            ->create([
//                                'email' => $data['Usuario'],
//                                'name' => $record->ApellidoPaterno . ' ' . $record->ApellidoMaterno . ', ' . $record->Nombres,
//                                'password' => $clave === ''? Hash::make($data['DNI']) : $clave,
//                                'IdEmpleado' => $record->IdEmpleado,
//                                'estado' => true,
//                            ]);
//                        if ($data['usuario_roles']) {
//                            $usuarioRelacion->syncRoles($data['usuario_roles']);
//                        }
//                    }
//                } else {
//                    $record->usuarioRelacion()->delete();
//                }
//
//            } else {
//                $record = Empleado::query()->create($data);
//
//                if ($data['Usuario']) {
//                    $usuarioRelacion = User::query()->create([
//                        'email' => $data['Usuario'],
//                        'name' => $record->ApellidoPaterno . ' ' . $record->ApellidoMaterno . ', ' . $record->Nombres,
//                        'password' => Hash::make($data['Clave']),
//                        'IdEmpleado' => $record->IdEmpleado,
//                        'estado' => true,
//                    ]);
//
//                    if ($data['usuario_roles']) {
//                        $usuarioRelacion->syncRoles($data['usuario_roles']);
//                    }
//                }
//            }
//
//            if ($data['ImagenFoto64']) {
//                if (!Storage::exists('fotos')) {
//                    Storage::makeDirectory('fotos');
//                }
//                $uuid = Str::uuid() . '.jpg';
//                $relativePath = 'fotos/' . $uuid;
//                $fullPath = storage_path('app/private/' . $relativePath);
//                $tempPath = tempnam(sys_get_temp_dir(), 'img_');
//                file_put_contents($tempPath, base64_decode($data['ImagenFoto64']));
//
//                $manager = new ImageManager(new Driver());
//                $image = $manager->read($tempPath);
//                $image->scale(height: 300);
//                $image->save($fullPath);
//                unlink($tempPath);
//
//                $record->update(['ImagenFoto' => $uuid]);
//            }
//
//            if ($data['ImagenFirma64']) {
//                if (!Storage::exists('firmas')) {
//                    Storage::makeDirectory('firmas');
//                }
//                $uuid = Str::uuid() . '.jpg';
//                $relativePath = 'firmas/' . $uuid;
//                $fullPath = storage_path('app/private/' . $relativePath);
//                $tempPath = tempnam(sys_get_temp_dir(), 'img_');
//                file_put_contents($tempPath, base64_decode($data['ImagenFirma64']));
//
//                $manager = new ImageManager(new Driver());
//                $image = $manager->read($tempPath);
//                $image->scale(height: 300);
//                $image->save($fullPath);
//                unlink($tempPath);
//
//                $record->update(['ImagenFirma' => $uuid]);
//            }
//
//            if ($data['roles']) {
//                foreach ($data['roles'] as $row) {
//                    $record->roles()->create([
//                        'IdRol' => $row['IdRol']
//                    ]);
//                }
//            }
//
//            if ($data['cargos']) {
//                foreach ($data['cargos'] as $row) {
//                    $record->cargos()->create([
//                        'idCargo' => $row['IdCargo']
//                    ]);
//                }
//            }
//
//            if ($data['lugaresTrabajos']) {
//                foreach ($data['lugaresTrabajos'] as $row) {
//                    $record->lugaresTrabajos()->create([
//                        'idLaboraArea' => $row['IdLaboraArea'],
//                        'idLaboraSubArea' => $row['IdLaboraSubArea']
//                    ]);
//                }
//            }
//
//            if ($data['esProfesionalSalud']) {
//                $medico = $record->medico()->updateOrCreate([
//                    'IdEmpleado' => $record->IdEmpleado
//                ], [
//                    'Colegiatura' => $request->input('Colegiatura'),
//                    'LoteHIS' => $request->input('LoteHIS'),
//                    'idColegioHIS' => $request->input('idColegioHIS'),
//                    'rne' => $request->input('rne'),
//                    'egresado' => $request->input('egresado'),
//                ]);
//
//                if ($data['especialidades']) {
//                    $medico->especialidades()->delete();
//                    foreach ($data['especialidades'] as $row) {
//                        $medico->especialidades()->create([
//                            'IdEspecialidad' => $row['IdEspecialidad'],
//                            'idEstado' => $row['idEstado']
//                        ]);
//                    }
//                }
//            } else {
//                $medico = $record->medico;
//                if ($medico) {
//                    $medico->especialidades()->delete();
//                    $medico->delete();
//                }
//            }
//
//            DB::commit();
//            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
//        } catch (\Throwable $th) {
//            DB::rollBack();
//            return obtener_respuesta_error($th->getMessage());
//        }
//    }

    public function recordActive($id)
    {
        $record = new EmpleadoResource(Empleado::query()->findOrFail($id));
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
            $record = Empleado::query()->find($request->input('id'));
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
        $record = new EmpleadoResource(Empleado::query()->findOrFail($id));
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
            $record = Empleado::query()->findOrFail($request->input('id'));
            $record->roles()->delete();
            $record->especialidades()->delete();
            $record->medico()->delete();
            $record->usuarioRelacion()->delete();
            $record->delete();
            cache_core_parametros_limpiar();
            DB::commit();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function generarUsuario(Request $request)
    {
        DB::beginTransaction();
        try {
            $codigo = $request->input('codigo');
            $empleado = Empleado::query()->find($codigo);

            // Generar nombre de usuario
            $usuarioNombre = $empleado->Usuario;

            if (empty($usuarioNombre)) {
                $usuarioNombre = str_to_lower_utf8(
                    trim($empleado->ApellidoPaterno) . Str::substr(trim($empleado->Nombre), 0, 1)
                );

                $empleado->update(['Usuario' => $usuarioNombre]);
            }

            $clave = Hash::make(trim($empleado->DNI));
            $nombreCompleto = trim($empleado->ApellidoPaterno . ' ' . $empleado->ApellidoMaterno . ', ' . $empleado->Nombres);

            $usuario = User::query()->where('IdEmpleado', $empleado->IdEmpleado)->first();

            if ($usuario) {
                $usuario->update([
                    'email' => $usuarioNombre,
                    'name' => $nombreCompleto,
                    'password' => $clave,
                ]);
            } else {
                User::query()->create([
                    'email' => $usuarioNombre,
                    'name' => $nombreCompleto,
                    'password' => $clave,
                    'IdEmpleado' => $empleado->IdEmpleado,
                    'estado' => true,
                ]);
            }

//            if ($empleado->Usuario !== '' && $empleado->Usuario !== null) {
//                $userName = $empleado->Usuario;
//            } else {
//                $userName = str_to_lower_utf8($empleado->ApellidoPaterno . Str::substr($empleado->Nombre, 0, 1));
//                $empleado->update([
//                    'Usuario' => $userName,
//                ]);
//            }
//
//            $claveActual = Hash::make($empleado->DNI);

//            $usuario = User::query()->where('IdEmpleado', $codigo)->first();
//            if ($usuario) {
//                $usuario->email = $userName;
//                $usuario->name = $empleado->ApellidoPaterno . ' ' . $empleado->ApellidoMaterno . ', ' . $empleado->Nombres;
//                $usuario->password = $claveActual;
//                $usuario->save();
//            } else {
//                User::query()->create([
//                    'email' => $userName,
//                    'name' => $empleado->ApellidoPaterno . ' ' . $empleado->ApellidoMaterno . ', ' . $empleado->Nombres,
//                    'password' => $claveActual,
//                    'IdEmpleado' => $codigo,
//                    'estado' => true,
//                ]);
//            }

            DB::commit();
            return [
                'success' => true,
                'mensaje' => 'Usuario generado correctamente',
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'mensaje' => $th->getMessage(),
            ];
        }
    }

    public function eliminarUsuario(Request $request)
    {
        DB::beginTransaction();
        try {
            $codigo = $request->input('codigo');
            $empleado = Empleado::query()->find($codigo);
            ConfiguracionDataTable::query()->where('user_id', $empleado->usuarioRelacion->id)->delete();
            $empleado->usuarioRelacion->delete();
            DB::commit();
            return [
                'success' => true,
                'mensaje' => 'Usuario eliminado correctamente',
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'mensaje' => $th->getMessage(),
            ];
        }
    }

    public function consultarColegiatura(Request $request)
    {
//        01	COLEGIO MEDICO DE PERU
//        02	COLEGIO QUIMICO FARMACEUTICO DEL PERU
//        03	COLEGIO ODONTOLOGICO DEL PERU
//        04	COLEGIO DE BIOLOGOS DEL PERU
//        05	COLEGIO DE OBSTETRICES DEL PERU
//        06	COLEGIO DE ENFERMEROS DEL PERU
//        07	COLEGIO DE TRABAJADORES SOCIALES DEL PER
//        08	COLEGIO DE PSICOLOGOS DEL PERU
//        09	COLEGIO TECNOLOGO MEDICO DEL PERU
//        10	COLEGIO DE NUTRICIONISTAS DEL PERU
        try {
            $idColegioHIS = $request->input('idColegioHIS');

            if ($idColegioHIS === '01') {
                $request->merge([
                    'apellido_paterno' => $request->input('ApellidoPaterno'),
                    'apellido_materno' => $request->input('ApellidoMaterno'),
                    'nombres' => $request->input('Nombres')
                ]);
                $data = (new DoctorController())->consultarPorNombre($request);
                return $this->formatearDoctor($data);
            } elseif ($idColegioHIS === '02') {
                throw new Exception('No se tiene acceso a la información de la colegiatura.');
            } elseif ($idColegioHIS === '03') {
                $request->merge([
                    'apellidos' => $request->input('ApellidoPaterno') . ' ' . $request->input('ApellidoMaterno'),
                    'nombres' => $request->input('Nombres')
                ]);
                $data = (new OdontologoController())->consultarPorNombre($request);
                return $this->formatearOdontologo($data);
            } elseif ($idColegioHIS === '04') {
                throw new Exception('No se tiene acceso a la información de la colegiatura.');
            } elseif ($idColegioHIS === '05') {
                $request->merge([
                    'apellido_paterno' => $request->input('ApellidoPaterno'),
                    'apellido_materno' => $request->input('ApellidoMaterno'),
                    'nombres' => $request->input('Nombres')
                ]);
                $data = (new ObstetraController())->consultarPorNombre($request);
                return $this->formatearObstetra($data);
            } elseif ($idColegioHIS === '06') {
                $request->merge([
                    'nombres' => $request->input('ApellidoPaterno') . ' ' .
                        $request->input('ApellidoMaterno') . ' ' .
                        $request->input('Nombres'),
                ]);
                $data = (new EnfermeraController())->consultarPorNombre($request);
                return $this->formatearEnfermera($data);
            } elseif ($idColegioHIS === '07') {
                throw new Exception('No se tiene acceso a la información de la colegiatura.');
            } elseif ($idColegioHIS === '08') {
                $request->replace(array_merge($request->all(), [
                    'nombres' => $request->input('ApellidoPaterno') . ' ' .
                        $request->input('ApellidoMaterno') . ' ' .
                        $request->input('Nombres'),
                ]));
                $data = (new PsicologoController())->consultarPorNombre($request);
                return $this->formatearPsicologo($data);
            } elseif ($idColegioHIS === '09') {
                throw new Exception('No se tiene acceso a la información de la colegiatura.');
            } elseif ($idColegioHIS === '10') {
                throw new Exception('No se tiene acceso a la información de la colegiatura.');
            } else {
                throw new Exception('Colegiatura inválida.');
            }
        } catch (\Throwable $th) {
            return obtener_respuesta_error($th->getMessage());
        }
    }

    private function formatearDoctor($data): array
    {
        if ($data['success']) {
            $especialidades = [];
            foreach ($data['data'] as $row) {
                foreach ($row['detalle_adicional']['especialidades'] as $especialidad) {
                    $especialidades[] = [
                        'colegiatura' => $row['cmp'],
                        'estado' => $row['detalle_adicional']['estado'],
                        'codigo' => $especialidad['codigo'],
                        'nombre' => $especialidad['registro'],
                    ];
                }
            }
            return [
                'success' => true,
                'data' => $especialidades,
                'original' => $data
            ];
        }
        return $data;
    }

    private function formatearOdontologo($data): array
    {
        if ($data['success']) {
            $especialidades = [];
            foreach ($data['data'] as $row) {
                $especialidades[] = [
                    'colegiatura' => $row['cop'],
                    'estado' => $row['estado'],
                    'codigo' => '',
                    'nombre' => '',
                ];
            }
            return [
                'success' => true,
                'data' => $especialidades,
                'original' => $data
            ];
        }
        return $data;
    }

    private function formatearObstetra($data): array
    {
        if ($data['success']) {
            $especialidades = [];
            foreach ($data['data'] as $row) {
                $especialidades[] = [
                    'colegiatura' => $row['cop'],
                    'estado' => $row['estado'],
                    'codigo' => '',
                    'nombre' => '',
                ];
            }
            return [
                'success' => true,
                'data' => $especialidades,
                'original' => $data
            ];
        }
        return $data;
    }

    private function formatearEnfermera($data): array
    {
        if ($data['success']) {
            $especialidades = [];
            foreach ($data['data'] as $row) {
                foreach ($row['especialidades'] as $especialidad) {
                    $especialidades[] = [
                        'colegiatura' => $row['cep'],
                        'estado' => $row['estado'],
                        'codigo' => $especialidad['registro'],
                        'nombre' => $especialidad['nombre'],
                    ];
                }
            }
            return [
                'success' => true,
                'data' => $especialidades,
                'original' => $data
            ];
        }
        return $data;
    }

    private function formatearPsicologo($data): array
    {
        if ($data['success']) {
            $especialidades = [];
            foreach ($data['data'] as $row) {
                $especialidades[] = [
                    'colegiatura' => $row['cpsp'],
                    'estado' => $row['estado'],
                    'codigo' => '',
                    'nombre' => '',
                ];
            }
            return [
                'success' => true,
                'data' => $especialidades,
                'original' => $data
            ];
        }
        return $data;
    }

    public function actualizarEmpleadoPorReniec(Request $request)
    {
        $record = Empleado::query()->find($request->input('id'));

        $request->merge([
            'dni' => trim($record->DNI),
        ]);
        $res = (new ReniecAvanzadoController())->consultar($request);

        if ($res['success']) {
            $_data = $res['data'];
            $data['ApellidoPaterno'] = $_data['ApellidoPaterno'];
            $data['ApellidoMaterno'] = $_data['ApellidoMaterno'];
            $data['Nombres'] = $_data['Nombres'];
            $data['idSexo'] = (int)$_data['Sexo'];
            $data['sexo'] = (int)$_data['Sexo'];

            $fechaNacimiento = explode('/', $_data['FechaNacimiento']);
            $data['FechaNacimiento'] = $fechaNacimiento[2] . '-' . $fechaNacimiento[1] . '-' . $fechaNacimiento[0];

            if (!is_null($_data['ImagenFoto']) && $_data['ImagenFoto'] !== '') {
                $res = ImagenBase64Helper::almacenarImagenBase64($_data['ImagenFoto'], 'empleado/fotos');
                if ($res['success']) {
                    $data['ImagenFoto'] = $res['uuid'];
                }
            }

            if (!is_null($_data['ImagenFirma']) && $_data['ImagenFirma'] !== '') {
                $res = ImagenBase64Helper::almacenarImagenBase64($_data['ImagenFirma'], 'empleado/firmas');
                if ($res['success']) {
                    $data['ImagenFirma'] = $res['uuid'];
                }
            }

            $record->update($data);

            return obtener_respuesta_exito('El empleado fue actualizado de forma exitosa.');
        }

        return $res;
    }
}
