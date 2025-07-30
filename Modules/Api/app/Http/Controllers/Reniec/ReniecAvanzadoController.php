<?php

namespace Modules\Api\Http\Controllers\Reniec;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReniecAvanzadoController extends Controller
{
    public function consultar(Request $request)
    {
        $dni = trim($request->input('dni'));

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
        $consulta = 'http://tempuri.org/obtenerDatosCompletos';

        return $this->getDataAdvanced($app, $usuario, $clave, $url, $consulta, $dni);
    }

    private function getDataAdvanced($app, $usuario, $clave, $url, $consulta, $dni): array
    {
        $start = microtime(true);

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
            <tem:obtenerDatosCompletos>
                <tem:nrodoc>$dni</tem:nrodoc>
            </tem:obtenerDatosCompletos>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $options = [
            'location' => $url,
            'uri' => 'http://tempuri.org/',
            'trace' => 1,
        ];

        try {
            $soapClient = new \SoapClient(null, $options);
            $responseXml = $soapClient->__doRequest($xmlRequest, $url, $consulta, SOAP_1_1);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($responseXml);

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
            $response = $body?->children('http://tempuri.org/')?->obtenerDatosCompletosResponse?->obtenerDatosCompletosResult ?? null;

            if (!$response) {
                return [
                    'success' => false,
                    'code' => '-',
                    'message' => 'Elemento obtenerDatosCompletosResult no encontrado.',
                    'time' => microtime(true) - $start,
                ];
            }

            $r = $response->string;
            $code = (string)$r[0];

            if ($code === '0000') {
                $fields = [
                    'CodigoRespuesta', //0
                    'MensajeRespuesta', //1
                    'NumeroDeDNI',//2
                    'DigitoVerificacion',//3
                    'ApellidoPaterno',//4
                    'ApellidoMaterno',//5
                    'ApellidoDeCasada',//6
                    'Nombres',//7
                    'UbigeoContinenteDomicilio',//8
                    'UbigeoPaisDomicilio',//9
                    'UbigeoDepartamentoDomicilio',//10
                    'UbigeoProvinciaDomicilio',//11
                    'UbigeoDistritoDomicilio',//12
                    'UbigeoLocalidadDomicilio',//13
                    'ContinenteDomicilio',//14
                    'PaisDomicilio',//15
                    'DepartamentoDomicilio',//16
                    'ProvinciaDomicilio',//17
                    'DistritoDomicilio',//18
                    'LocalidadDomicilio',//19
                    'EstadoCivil',//20
                    'CodigoGradoInstruccion',//21
                    'Sexo',//22
                    'UbigeoDepartamentoNacimiento',//23
                    'UbigeoProvinciaNacimiento',//24
                    'UbigeoDistritoNacimiento',//25
                    'DepartamentoNacimiento',//26
                    'ProvinciaNacimiento',//27
                    'DistritoNacimiento',//28
                    'FechaNacimiento',//29
                    'NombrePadre',//30
                    'NombreMadre',//31
                    'FechaInscripcion',//32
                    'FechaExpedicion',//33
                    'Restricciones',//34
                    'PrefijoDireccion',//35
                    'Direccion',//36
                    'NumeroDireccion',//37
                    'BlockChalet',//38
                    'PrefijoBlockChalet',//39
                    'Interior',//40
                    'Urbanizacion',//41
                    'Etapa',//42
                    'Manzana',//43
                    'Lote',//44
                    'PrefijoDptoPisoInterior',//45
                    'PrefijoUrbCondResid',//46
                    'ImagenFoto',//47
                    'ImagenFirma',//48
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
                'message' => 'Error código: ' . $code,
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

//    private function getDataAdvanced($app, $usuario, $clave, $url, $consulta, $dni)
//    {
//        $time_start = microtime(true);
//
//        $xmlRequest = <<<XML
//        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
//        <soapenv:Header>
//            <tem:Credencialmq>
//                <tem:app>$app</tem:app>
//                <tem:usuario>$usuario</tem:usuario>
//                <tem:clave>$clave</tem:clave>
//            </tem:Credencialmq>
//        </soapenv:Header>
//        <soapenv:Body>
//            <tem:obtenerDatosCompletos>
//                <tem:nrodoc>$dni</tem:nrodoc>
//            </tem:obtenerDatosCompletos>
//        </soapenv:Body>
//        </soapenv:Envelope>
//        XML;
//
//        $options = [
//            'location' => $url,
//            'uri' => 'http://tempuri.org/',
//            'trace' => 1,
//        ];
//
//        $soapClient = new \SoapClient(null, $options);
//        try {
//            $data_reniec = $soapClient->__doRequest($xmlRequest, $url, $consulta, SOAP_1_1);
//            libxml_use_internal_errors(true);
//            $xml = simplexml_load_string($data_reniec);
//            if ($xml === false) {
//                $errors = [];
//                foreach (libxml_get_errors() as $error) {
//                    $error[] = $error->message;
//                }
//                libxml_clear_errors();
//                $res = [
//                    'success' => false,
//                    'code' => 0,
//                    'message' => implode(', ', $errors)
//                ];
//            } else {
//                $response = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
//                    ->children('http://tempuri.org/')->obtenerDatosCompletosResponse->obtenerDatosCompletosResult;
//
//                if ($response) {
//                    $r = $response->string;
//                    $code = (string)$r[0];
//                    if ($code === '0000') {
//                        $data = [
//                            'CodigoRespuesta' => (string)$r[0],
//                            'MensajeRespuesta' => (string)$r[1],
//                            'NumerodeDNI' => (string)$r[2],
//                            'DigitoVerificacion' => (string)$r[3],
//                            'ApellidoPaterno' => (string)$r[4],
//                            'ApellidoMaterno' => (string)$r[5],
//                            'ApellidoDeCasada' => (string)$r[6],
//                            'Nombres' => (string)$r[7],
//                            'UbigeoContinDomicilio' => (string)$r[8],
//                            'UbigeoPaisDomicilio' => (string)$r[9],
//                            'UbigeoDepartDomicilio' => (string)$r[10],
//                            'UbigeoProvinDomicilio' => (string)$r[11],
//                            'UbigeoDistritDomicilio' => (string)$r[12],
//                            'UbigeoLocaliDomicilio' => (string)$r[13],
//                            'ContinenteDomicilio' => (string)$r[14],
//                            'PaisDomicilio' => (string)$r[15],
//                            'DepartDomicilio' => (string)$r[16],
//                            'ProvinDomicilio' => (string)$r[17],
//                            'DistritoDomicilio' => (string)$r[18],
//                            'LocalidaDomicilio' => (string)$r[19],
//                            'EstadoCivil' => (string)$r[20],
//                            'CodigodGradoInstruccion' => (string)$r[21],
//                            'Sexo' => (string)$r[22],
//                            'UbigeoDepartNacimiento' => (string)$r[23],
//                            'UbigeoProvinNacimiento' => (string)$r[24],
//                            'UbigeoDistritNacimiento' => (string)$r[25],
//                            'DepartamentoNacimiento' => (string)$r[26],
//                            'ProvinciaNacimiento' => (string)$r[27],
//                            'DistritoNacimiento' => (string)$r[28],
//                            'FechaNacimiento' => (string)$r[29],
//                            'NombrePadre' => (string)$r[30],
//                            'NombreMadre' => (string)$r[31],
//                            'FechaInscripcion' => (string)$r[32],
//                            'FechaExpedicion' => (string)$r[33],
//                            'Restricciones' => (string)$r[34],
//                            'Prefijodireccion' => (string)$r[35],
//                            'Direccion' => (string)$r[36],
//                            'NumeroDireccion' => (string)$r[37],
//                            'BlockChalet' => (string)$r[38],
//                            'PrefijoBlockChalet' => (string)$r[39],
//                            'Interior' => (string)$r[40],
//                            'Urbanizacion' => (string)$r[41],
//                            'Etapa' => (string)$r[42],
//                            'Manzana' => (string)$r[43],
//                            'Lote' => (string)$r[44],
//                            'PrefijoDptoPisoInterior' => (string)$r[45],
//                            'PrefijoUrbCondResid' => (string)$r[46],
//                            'ImagenFoto' => (string)$r[47],
//                            'ImagenFirma' => (string)$r[48],
//                        ];
//
//                        $res = [
//                            'success' => true,
//                            'code' => $code,
//                            'message' => 'Consulta satisfactoria',
//                            'data' => $data,
//                        ];
//                    } else {
//                        $res = [
//                            'success' => false,
//                            'code' => $code,
//                            'message' => 'Error código: ' . $code,
//                        ];
//                    }
//
//                } else {
//                    $res = [
//                        'success' => false,
//                        'code' => '-',
//                        'message' => 'Elemento obtenerDatosBasicosResult no encontrado en el XML.',
//                    ];
//                }
//            }
//        } catch (\SoapFault $sf) {
//            $res = [
//                'success' => false,
//                'code' => $sf->getCode(),
//                'message' => $sf->getMessage(),
//            ];
//        } catch (\Exception $e) {
//            $res = [
//                'success' => false,
//                'code' => $e->getCode(),
//                'message' => $e->getMessage(),
//            ];
//        }
//        $time_end = microtime(true);
//        $res['time'] = $time_end - $time_start;
//
//        return $res;
//    }
}
