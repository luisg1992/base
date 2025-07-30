<?php

namespace Modules\Api\Http\Controllers\Sis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Throwable;

class SisContingenciaController extends Controller
{
    public function consultarContingencia(Request $request)
    {
        $tipo = $request->input('tipo');
        $numero = $request->input('numero');

        $url = 'https://contingenciasis.minsa.gob.pe/frmConsultaContingencia.aspx';

        $initialResponse = Http::withoutVerifying()->get($url);

        if (!$initialResponse->successful()) {
            return ['success' => false, 'message' => 'No se pudo acceder al portal de contingencia.'];
        }

        $html = mb_convert_encoding($initialResponse->body(), 'UTF-8', 'ISO-8859-1');
        $doc = new Document($html);

        $viewstate = $doc->find('input[name=__VIEWSTATE]')[0]->getAttribute('value');
        $eventvalidation = $doc->find('input[name=__EVENTVALIDATION]')[0]->getAttribute('value');

        $params = [
            'hdnTipo' => '2',
            'cboTipoBusqueda' => '2',
            'txtApePaterno' => '',
            'txtApeMaterno' => '',
            'txtPriNombre' => '',
            'cboTipoDocumento' => $tipo,
            'txtNroDocumento' => $numero,
            'btnConsultar' => 'Consultar',
            '__VIEWSTATE' => $viewstate,
            '__EVENTVALIDATION' => $eventvalidation
        ];

        try {
            $response = Http::asForm()
                ->withoutVerifying()
                ->timeout(10)
                ->post($url, $params);

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'No se pudo acceder al portal del MINSA.'];
            }

            $doc = new Document($response->body());
            $rows = $doc->find('.c_texto_02');

            if (count($rows) > 1) {
                $tds = $rows[1]->find('td');
                if (count($tds) >= 16) {
                    $fechaAfiliacion = Carbon::createFromFormat('d/m/Y', trim($tds[5]->text()))->format('Y-m-d');
                    $data = [
                        'tipo_de_seguro' => preg_replace('/\s+/u', ' ', trim($tds[1]->text())),
                        'tipo_de_formato' => trim($tds[2]->text()),
                        'numero_afiliacion' => trim($tds[3]->text()),
                        'plan_de_beneficios' => trim($tds[4]->text()),
                        'fecha_de_afiliacion' => $fechaAfiliacion,
                        'vigencia_hasta' => preg_replace('/\s+/u', ' ', trim($tds[6]->text())),
                        'tipo_de_documento' => trim($tds[7]->text()),
                        'numero_de_documento' => trim($tds[8]->text()),
                        'apellido_paterno' => trim($tds[9]->text()),
                        'apellido_materno' => trim($tds[10]->text()),
                        'nombres' => trim($tds[11]->text()),
                        'fecha_de_nacimiento' => trim($tds[12]->text()),
                        'sexo' => trim($tds[13]->text()),
                        'eess' => trim($tds[14]->text()),
                        'ubicacion_eess' => trim($tds[15]->text()),
                    ];

                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            }

            return ['success' => false, 'message' => 'No se encontraron datos de afiliación.'];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al consultar: ' . $e->getMessage(),
                'exception' => class_basename($e),
            ];
        }
    }
}
