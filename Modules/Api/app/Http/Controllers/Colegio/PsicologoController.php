<?php

namespace Modules\Api\Http\Controllers\Colegio;

use App\Http\Controllers\Controller;
use DiDom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PsicologoController extends Controller
{
    public function consultarPorNombre(Request $request)
    {
        $nombres = $request->input('nombres');

        if (!$nombres) {
            return [
                'success' => false,
                'message' => 'El campo nombres es requerido',
            ];
        }

        return $this->searchCpsp('APELLIDOS', $nombres);
    }

    public function consultarPorCpsp(Request $request)
    {
        $cpsp = $request->input('cpsp');

        if (!$cpsp) {
            return [
                'success' => false,
                'message' => 'El campo cpsp es requerido',
            ];
        }

        return $this->searchCpsp('COLEGIATURA', $cpsp);
    }

    private function searchCpsp(string $tipo, string $valor): array
    {
        $start = microtime(true);
        try {
            $url = 'https://sistemascpsp.com/Form/busquedas/busqueda_colegiados_funciones.php';
            $params = [
                'param' => 0,
                'txtopciones' => $tipo,
                'txtdato_buscar' => $valor,
            ];

            $response = Http::timeout(5)
                ->withOptions(['verify' => false])
                ->asForm()
                ->post($url, $params);

            $response->throw();

            $doc = new Document(utf8_encode($response->body()));
            $rows = $doc->find('.table tr');

            if (count($rows) <= 1) {
                return [
                    'success' => false,
                    'message' => 'No se encontraron psicólogos',
                    'time' => microtime(true) - $start,
                ];
            }

            unset($rows[0]);

            $records = [];
            foreach ($rows as $row) {
                $tds = $row->find('td');
                if (count($tds) !== 3) {
                    continue;
                }

                $data = explode('|', $tds[1]->html());

                if (count($data) < 3) {
                    continue;
                }

                $line1 = utf8_decode($data[1]);
                $line2 = utf8_decode($data[2]);

                $line1_parts = explode('<br>', $line1);
                $line2_parts = explode('</b>', $line2);

                $entry = [
                    'cpsp' => trim(strip_tags($data[0])),
                    'nombres' => trim(strip_tags($line1_parts[0] ?? '')),
                    'cdr' => trim(strip_tags($line1_parts[1] ?? '')),
                    'estado' => trim(strip_tags($line2_parts[0] ?? '')),
                ];

                $records[] = $entry;

                if ($tipo === 'COLEGIATURA') {
                    break; // Solo uno
                }
            }

            return [
                'success' => count($records) > 0,
                'data' => $tipo === 'COLEGIATURA' ? $records[0] ?? [] : $records,
                'message' => count($records) > 0 ? 'Consulta satisfactoria' : 'No se encontraron psicólogos',
                'time' => microtime(true) - $start,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'time' => microtime(true) - $start,
            ];
        }
    }
}
