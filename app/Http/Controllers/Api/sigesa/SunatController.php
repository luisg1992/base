<?php

namespace App\Http\Controllers\Api\sigesa;

use DiDom\Document;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class SunatController extends Controller
{
    public function ruc(Request $request)
    {
        $number = $request->input('ruc');
        $timeout = 10;
        $time_start = microtime(true);

        $form_params = [
            'accion' => 'consPorRazonSoc',
            'razSoc' => 'BVA FOODS'
        ];
        $res = [];
        try {
            $response = Http::timeout($timeout)
                ->get('https://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias');

            $cookies = [
                'ITMRCONSRUCSESSION=' . $response->cookies()->getCookieByName('ITMRCONSRUCSESSION')->getValue(),
                'TS01fda901=' . $response->cookies()->getCookieByName('TS01fda901')->getValue()
            ];

            $response = Http::withHeaders([
                'Cookie' => $cookies
            ])
                ->asForm()
                ->timeout($timeout)
                ->post('https://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias', $form_params);

            if ($response->ok()) {
                $document = new Document(utf8_encode($response->body()));
                $name = $document->find('input[name=numRnd]');
                $random = $name[0]->getAttribute('value');
                $res = $this->getData($number, $random, $cookies);
            }
            $response->throw();
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'message' => $ee->getMessage(),
            ];
        }

        $time_end = microtime(true);

        $res['time'] = $time_end - $time_start;

        return $res;
    }

    public function getData($number, $random, $cookies)
    {
        $res = [];
        try {
            $timeout = 10;
            $form_params = [
                'accion' => 'consPorRuc',
                'nroRuc' => $number,
                'numRnd' => $random,
                'actReturn' => '1',
                'modo' => '1',
            ];

            $response = Http::withHeaders([
                'Cookie' => $cookies
            ])
                ->asForm()
                ->timeout($timeout)
                ->post('https://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias', $form_params);

            $doc = new Document(utf8_encode($response->body()));
            $rows = $doc->find('.list-group-item');
            if (count($rows) > 1) {
                $ruc_nombre_o_razon_social = $rows[0]->find('h4');
                $row1_array = explode('-', trim($ruc_nombre_o_razon_social[1]->text()));

                $direccion = '';
                $direccion_completa = '';
                $departamento = '';
                $provincia = '';
                $distrito = '';

                if (str_starts_with($number, '2')) {
                    $estado = $rows[4]->find('.list-group-item-text');
                    $condicion = $rows[5]->find('.list-group-item-text');

                    $direccion = $rows[6]->find('.list-group-item-text');
                    $direccion = trim($direccion[0]->text());
                    $items = explode('                                               -', $direccion);
                    if (3 !== count($items)) {
                        $direccion = preg_replace("[\s+]", ' ', $direccion);
                    } else {
                        $pieces = explode(' ', trim($items[0]));
                        $departamento = $this->getDepartment(end($pieces));
                        $provincia = trim($items[1]);
                        $distrito = trim($items[2]);
                        $removeLength = count(explode(' ', $departamento));
                        array_splice($pieces, -1 * $removeLength);
                        $direccion = rtrim(join(' ', $pieces));
                        $direccion_completa = $direccion . ', ' . $departamento . ' - ' . $provincia . ' - ' . $distrito;
                    }
                } else {
                    $estado = $rows[5]->find('.list-group-item-text');
                    $condicion = $rows[6]->find('.list-group-item-text');
                }

                $data = [
                    "ruc" => trim($row1_array[0]),
                    "nombre_o_razon_social" => trim($row1_array[1]),
                    "estado" => trim($estado[0]->text()),
                    "condicion" => trim($condicion[0]->text()),
                    "direccion" => $direccion,
                    "direccion_completa" => $direccion_completa,
                    "departamento" => $departamento,
                    "provincia" => $provincia,
                    "distrito" => $distrito,
                ];

                $res = [
                    'success' => true,
                    'data' => $data,
                ];
            }
            $response->throw();
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'message' => $ee->getMessage(),
            ];
        }

        return $res;
    }

    private function getDepartment($department)
    {
        $department = strtoupper($department);
        if (isset($this->overridDeps[$department])) {
            $department = $this->overridDeps[$department];
        }

        return $department;
    }
}
