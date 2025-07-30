<?php

namespace Modules\Api\Http\Controllers\Colegio;

use App\Http\Controllers\Controller;
use DiDom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnfermeraController extends Controller
{
    public function consultarPorNombre(Request $request)
    {
        $nombres = $request->input('nombres');

        if (empty($nombres)) {
            return $this->errorResponse('El campo nombres es requerido');
        }

        $start = microtime(true);
        try {
            $url = 'https://www.cep.org.pe/validar/comun/json_personas.php?query=' . urlencode($nombres);
            $response = Http::timeout(10)->withOptions(['verify' => false])->get($url);
            $response->throw();

            $records = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->body()), false) ?? [];
            $nurses = [];

            foreach ($records as $row) {
                $detail = $this->searchByCepWithData($row->c_cmp);
                if ($detail['success']) {
                    $nurses[] = $detail['data'];
                }
            }

            return [
                'success' => count($nurses) > 0,
                'data' => $nurses,
                'message' => count($nurses) ? null : 'No se encontraron enfermeras',
                'time' => microtime(true) - $start,
            ];

        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), $start);
        }
    }

    public function consultarPorCep(Request $request)
    {
        $cep = $request->input('cep');
        if (empty($cep)) {
            return $this->errorResponse('El campo cep es requerido');
        }
        return $this->searchByCepWithData($cep);
    }

    public function searchByCepWithData($cep)
    {
        $start = microtime(true);
        try {
            $token = $this->getCepToken();
            if (!$token) {
                return $this->errorResponse('No es posible consultar', $start, true);
            }

            $url = 'https://www.cep.org.pe/validar/pagina/view.php';
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->asForm()
                ->post($url, ['cep' => $cep, 'token' => $token]);
            $response->throw();

            $document = new Document(utf8_encode($response->body()));
            $nombres = trim(optional($document->first('h4'))->text());
            if (!$nombres) {
                return $this->errorResponse('No se encontró información', $start, false);
            }

            $foto = optional($document->first('img'))->attr('src');
            $consejo_regional = trim(optional($document->first('h6'))->text());
            $estado = trim(optional($document->first('div[role=alert]'))->text());

            $especialidades = [];
            $doc_especialidades = $document->first('table.table');
            if ($doc_especialidades) {
                foreach ($doc_especialidades->find('tbody')[0]->find('tr') ?? [] as $tr) {
                    $tds = $tr->find('td');
                    $especialidades[] = [
                        'grado' => trim($tds[1]->text() ?? ''),
                        'registro' => trim($tds[2]->text() ?? ''),
                        'nombre' => trim($tds[3]->text() ?? ''),
                    ];
                }
            }

            return [
                'success' => true,
                'data' => [
                    'cep' => $cep,
                    'nombres' => $nombres,
                    'estado' => $estado,
                    'consejo_regional' => $consejo_regional,
                    'foto' => $foto,
                    'especialidades' => $especialidades,
                ],
                'time' => microtime(true) - $start
            ];

        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), $start);
        }
    }

    /**
     * Obtiene el token de la página CEP.
     */
    private function getCepToken(): ?string
    {
        $response = Http::timeout(10)
            ->withOptions(['verify' => false])
            ->get('https://www.cep.org.pe/validar/');

        if ($response->ok()) {
            $document = new Document(utf8_encode($response->body()));
            return optional($document->first('input[name=token]'))->attr('value');
        }
        return null;
    }

    /**
     * Genera una respuesta de error.
     */
    private function errorResponse($message, $start = null, $fatal = false)
    {
        $error = [
            'success' => false,
            'message' => $message,
        ];
        if ($start) {
            $error['time'] = microtime(true) - $start;
        }
        if ($fatal) {
            $error['fatal'] = true;
        }
        return $error;
    }
}
