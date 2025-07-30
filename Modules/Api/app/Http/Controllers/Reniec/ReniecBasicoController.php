<?php

namespace Modules\Api\Http\Controllers\Reniec;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReniecBasicoController extends Controller
{
    public function consultar(Request $request)
    {
        $dni = $request->input('dni');

        if (!validar_dni($dni)) {
            return [
                'success' => false,
                'message' => 'El número de DNI ingresado es inválido',
            ];
        }

        $app = 'HNDM';
        $usuario = '06816965';
        $clave = 'H@@i/8d_#%M@i9$';
        $url = 'http://wsvmin.minsa.gob.pe/wsreniecmq/serviciomq.asmx?WSDL';
        $consulta = 'http://tempuri.org/obtenerDatosBasicos';

        return $this->getDataBasic($app, $usuario, $clave, $url, $consulta, $dni);
    }

    private function getDataBasic($app, $usuario, $clave, $url, $consulta, $dni): array
    {
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
        <soapenv:Header>
            <tem:Credencialmq>
                <tem:app>$app</tem:app>
                <tem:usuario>$usuario</tem:usuario>
                <tem:clave>$clave</tem:clave>
            </tem:Credencialmq>
        </soapenv:Header>
        <soapenv:Body>
            <tem:obtenerDatosBasicos>
                <tem:nrodoc>$dni</tem:nrodoc>
            </tem:obtenerDatosBasicos>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $options = [
            'location' => $url,
            'uri' => 'http://tempuri.org/',
            'trace' => 1,
        ];

        $start = microtime(true);

        try {
            $soapClient = new \SoapClient(null, $options);
            $rawResponse = $soapClient->__doRequest($xmlRequest, $url, $consulta, SOAP_1_1);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($rawResponse);

            if ($xml === false) {
                $errors = array_map(fn($e) => trim($e->message), libxml_get_errors());
                libxml_clear_errors();

                return [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Error al parsear XML: ' . implode(' | ', $errors),
                    'time' => microtime(true) - $start,
                ];
            }

            $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body ?? null;
            $response = $body?->children('http://tempuri.org/')?->obtenerDatosBasicosResponse?->obtenerDatosBasicosResult ?? null;

            if (!$response) {
                return [
                    'success' => false,
                    'code' => '-',
                    'message' => 'Elemento obtenerDatosBasicosResult no encontrado.',
                    'time' => microtime(true) - $start,
                ];
            }

            $r = $response->string;
            $code = (string)$r[0];

            if ($code === '0000') {
                $fields = [
                    'CodigoRespuesta',  //0
                    'MensajeRespuesta', //1
                    'ApellidoPaterno',  //2
                    'ApellidoMaterno',  //3
                    'ApellidoDeCasada', //4
                    'Nombres',          //5
                    'UbigeoContinenteDomicilio', //6
                    'UbigeoPaisDomicilio', //7
                    'UbigeoDepartamentoDomicilio', //8
                    'UbigeoProvinciaDomicilio', //9
                    'UbigeoDistritoDomicilio', //10
                    'UbigeoDistritoLocalidad',  //11
                    'ContinenteDomicilio', //12
                    'PaisDomicilio', //13
                    'DepartamentoDomicilio', //14
                    'ProvinciaDomicilio', //15
                    'DistritoDomicilio',  //16
                    'LocalidadDomicilio',  //17
                    'Direccion', //18
                    'Sexo', //19
                    'FechaNacimiento', //20
                    'FechaExpedicion', //21
                    'NumeroDocumento'  //22
                ];

                $data = [];
                foreach ($fields as $i => $key) {
                    $data[$key] = (string)$r[$i];
                }

                return [
                    'success' => true,
                    'code' => $code,
                    'message' => 'Consulta satisfactoria',
                    'data' => $data,
                    'time' => microtime(true) - $start,
                ];
            }

            return [
                'success' => false,
                'code' => $code,
                'message' => "Error en respuesta. Código: {$code}",
                'time' => microtime(true) - $start,
            ];

        } catch (\SoapFault $sf) {
            return [
                'success' => false,
                'code' => $sf->getCode(),
                'message' => 'SOAP Fault: ' . $sf->getMessage(),
                'time' => microtime(true) - $start,
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'code' => $th->getCode(),
                'message' => 'Error: ' . $th->getMessage(),
                'time' => microtime(true) - $start,
            ];
        }
    }
}
