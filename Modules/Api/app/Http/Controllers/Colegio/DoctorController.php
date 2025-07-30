<?php

namespace Modules\Api\Http\Controllers\Colegio;

use App\Http\Controllers\Controller;
use DiDom\Document;
use ErrorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class DoctorController extends Controller
{
    public function consultarPorNombre(Request $request)
    {
        $params = [
            'appaterno' => remove_accents($request->input('apellido_paterno')),
            'apmaterno' => remove_accents($request->input('apellido_materno')),
            'nombres' => remove_accents($request->input('nombres')),
        ];

        if (collect($params)->every(fn($v) => is_null($v) || $v === '')) {
            return [
                'success' => false,
                'message' => 'Mínimo uno de los campos es requerido'
            ];
        }
        $params['cmp'] = '';
        $params['Submit'] = 'Buscar';

        return $this->searchByFormParams($params);
    }

    public function consultarPorCmp(Request $request)
    {
        $cmp = $request->input('cmp');
        if (empty($cmp)) {
            return [
                'success' => false,
                'message' => 'El campo cmp es requerido'
            ];
        }
        $params = [
            'cmp' => $cmp,
            'appaterno' => '',
            'apmaterno' => '',
            'nombres' => '',
            'Submit' => 'Buscar',
        ];
        return $this->searchByFormParams($params);
    }

    private function searchByFormParams(array $form_params)
    {
        $start = microtime(true);
        $url = 'https://aplicaciones.cmp.org.pe/conoce_a_tu_medico/datos-colegiado.php';

        try {
            $response = Http::timeout(10)->asForm()->post($url, $form_params);
            $response->throw();

            if (!$response->ok()) {
                return [
                    'success' => false,
                    'message' => 'No se pudo obtener respuesta del servidor',
                    'time' => microtime(true) - $start
                ];
            }

            $document = new Document($response->body());
            $doctors = $this->parseDoctorsTable($document);

            return [
                'success' => count($doctors) > 0,
                'data' => $doctors,
                'message' => count($doctors) ? 'Consulta satisfactoria' : 'No se encontraron doctores',
                'time' => microtime(true) - $start
            ];
        } catch (ConnectionException $e) {
            return $this->errorResponse($e, Response::HTTP_GATEWAY_TIMEOUT, $start);
        } catch (RequestException|ErrorException|\Throwable $e) {
            return $this->errorResponse($e, $e->getCode(), $start);
        }
    }

    private function parseDoctorsTable($document): array
    {
        $doctors = [];
        $table = $document->find('table')[0] ?? null;
        if (!$table) return [];

        $rows = $table->find('tr');
        foreach (array_slice($rows, 1) as $row) {
            $cols = $row->find('td');
            if (count($cols) < 5) continue;

            $id = '';
            $href = $cols[0]->find('a')[0]->attr('href') ?? null;
            if ($href) {
                $parts = explode('=', $href);
                $id = $parts[1] ?? '';
            }

            $detalle = [];
            if ($id) {
                $detailRes = $this->searchDetailByIdWithData($id);
                if ($detailRes['success']) {
                    $detalle = collect($detailRes['data'])->except('cmp', 'apellidos', 'nombres')->toArray();
                }
            }

            $doctors[] = [
                'id' => $id,
                'cmp' => $cols[1]->text(),
                'apellido_paterno' => $cols[2]->text(),
                'apellido_materno' => $cols[3]->text(),
                'nombres' => $cols[4]->text(),
                'detalle_adicional' => $detalle,
            ];
        }
        return $doctors;
    }

    private function errorResponse($e, $code, $start)
    {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $code,
            'time' => microtime(true) - $start
        ];
    }

    public function searchDetailById(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            return [
                'success' => false,
                'message' => 'El campo id es requerido'
            ];
        }
        return $this->searchDetailByIdWithData($id);
    }

    public function searchDetailByIdWithData($id)
    {
        $start = microtime(true);
        $urlBase = 'https://aplicaciones.cmp.org.pe/conoce_a_tu_medico/';
        $url = $urlBase . 'datos-colegiado-detallado.php?id=' . $id;

        try {
            $response = Http::timeout(10)->get($url);
            $response->throw();

            if (!$response->ok()) {
                return [
                    'success' => false,
                    'message' => 'No se pudo obtener respuesta del servidor',
                    'time' => microtime(true) - $start
                ];
            }

            $document = new Document($response->body());
            $tables = $document->find('table');
            if (count($tables) < 4) {
                throw new \Exception('Estructura inesperada de datos');
            }

            // Datos principales
            $trs = $tables[0]->find('tr');
            $tds = $trs[1]->find('td');
            $cmp = $tds[0]->text();
            $apellidos = $tds[1]->text();
            if (str_contains($apellidos, 'Undefined variable')) {
                throw new \Exception('No se encontró información');
            }
            $nombres = $tds[2]->text();

            // Estado
            $estado = trim($tables[1]->find('tr')[0]->find('td')[0]->text());

            // Foto y datos adicionales
            $trFoto = $tables[2]->find('tr')[1];
            $tdFoto = $trFoto->find('td');
            $foto = trim($tdFoto[0]->find('img')[0]->attr('src'));
            $informacion_adicional_general = trim($tdFoto[1]->text());
            $consejo_regional = trim($tdFoto[2]->text());

            // Especialidades
            $especialidades = [];
            foreach (array_slice($tables[3]->find('tr'), 1) as $espTr) {
                $tdEsp = $espTr->find('td');
                if (count($tdEsp) < 4) continue;
                $especialidades[] = [
                    'registro' => trim($tdEsp[0]->text()),
                    'tipo' => trim($tdEsp[1]->text()),
                    'codigo' => trim($tdEsp[2]->text()),
                    'informacion_adicional' => trim($tdEsp[3]->text()),
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'cmp' => $cmp,
                    'apellidos' => $apellidos,
                    'nombres' => $nombres,
                    'estado' => $estado,
                    'consejo_regional' => $consejo_regional,
                    'informacion_adicional' => $informacion_adicional_general,
                    'foto' => $urlBase . $foto,
                    'url' => $url,
                    'especialidades' => $especialidades,
                ],
                'time' => microtime(true) - $start
            ];
        } catch (ConnectionException $e) {
            return $this->errorResponse($e, Response::HTTP_GATEWAY_TIMEOUT, $start);
        } catch (RequestException|ErrorException|\Throwable $e) {
            return $this->errorResponse($e, $e->getCode(), $start);
        }
    }
}
